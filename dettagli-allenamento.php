<?php
	require_once "db.php";
	use DB\DBAccess;

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		$id = $_GET['id'];
		$queryOverviewAllenamentoResult = $connessione->doReadQuery("SELECT id, nome, descrizione, allenamento.username_utente, data_creazione, COUNT(id) AS Followers FROM allenamento LEFT JOIN utente_allenamento ON id = id_allenamento WHERE id = ?", "i", $id);
		$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = ?", "i", $id);
		
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
			$queryIsClientResult = $connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"]);
			$connection_statement = ($queryIsClientResult[0])? 1 : 0;
		} else {
			$connection_statement = 2;
		}
		
		$connessione->closeConnection();

		$content = "<form action='' method='post'><input type='hidden' name='isAdmin' value='" . $connection_statement . "' readonly><button type='submit'>Crea allenamento</button></form>";

		if (!(isset($_POST['elimina']) && $_POST['id'] == $queryOverviewAllenamentoResult[0]['id'])) {
			$content .= '<h2>' . $queryOverviewAllenamentoResult[0]['nome'] . '</h2>';
			$content .= '<p>' . $queryOverviewAllenamentoResult[0]['descrizione'] . '</p>';
			$numeroEsercizi = count($queryDettaglioAllenamentoResult);
			$content .= '<p>Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';
			
			if ($numeroEsercizi == 1) {
				$content .= 'o';
			}
			
			$content .= ', tra cui esercizi come ' . $queryDettaglioAllenamentoResult[0]['nome'];
			
			if ($numeroEsercizi > 1) {
				$i = 1;
				for (; $i <= $numeroEsercizi - 3 && $i <= 3; $i++) {
					$content .= ', ' . $queryDettaglioAllenamentoResult[$i]['nome'];
				}
				$content .= ' e ' . $queryDettaglioAllenamentoResult[$i]['nome'];
			}

			$content .= '.</p>';
			$content .= '<ul>';
			$content .= '<li>' . $queryOverviewAllenamentoResult[0]['username_utente'] . '</li>';
			$content .= '<li>' . $queryOverviewAllenamentoResult[0]['data_creazione'] . '</li>';
			$content .= '<li>' . ($queryOverviewAllenamentoResult[0]['Followers'] == null ? 0 : $queryOverviewAllenamentoResult[0]['Followers']) . '</li>';
			$content .= '</ul>';
			$content .= '<ul>';

			if ($connection_statement == 2 || ($connection_statement == 0 && $queryOverviewAllenamentoResult[0]['username_utente'] == $_SESSION["username"])) { //admin: 1
				$content .= "<form action='' method='post'><input type='hidden' name='id' value='" . $queryOverviewAllenamentoResult[0]['id'] . "' readonly><input type='hidden' name='isAdmin' value='" . $connection_statement . "' readonly><button type='submit'>Modifica allenamento</button></form>";
				$content .= "<form action='dettagli-allenamento.php?pagina=" . $_GET['pagina'] . "&percorso=" . $_GET['percorso'] . "&nomeBreadcrumb=" . $_GET['nomeBreadcrumb'] . " method='post'><input type='hidden' name='id' value='" . $queryOverviewAllenamentoResult[0]['id'] . "' readonly><button type='submit' name='elimina'>Elimina allenamento</button></form>";
			} //else...
			if ($connection_statement == 2) { //cliente: 0
				$segui = true;
				if (isset($_POST['segui']) && $_POST['id'] == $queryOverviewAllenamentoResult[0]['id']) {
					if ($_POST['segui'] == "seguire") {
						$connessione->doWriteQuery("INSERT INTO utente_allenamento(username_utente, id_allenamento) VALUES (?, ?)", "si", "admin", $queryOverviewAllenamentoResult[0]['id']);
						$segui = false;
						$content .= "<p>Hai iniziato a seguire questo allenamento!</p>";
					} else {
						$connessione->doWriteQuery("DELETE FROM utente_allenamento WHERE id = ?", "i", $queryOverviewAllenamentoResult[0]['id']);
						$segui = true;
						$content .= "<p>Hai smesso di seguire questo allenamento!</p>";
					}
				}
					if ($segui) {
					$content .= "<form action='dettagli-allenamento.php?pagina=" . $_GET['pagina'] . "&percorso=" . $_GET['percorso'] . "&nomeBreadcrumb=" . $_GET['nomeBreadcrumb'] . " method='post'><input type='hidden' name='id' value='" . $queryOverviewAllenamentoResult[0]['id'] . "' readonly><button type='submit' name='segui' value='seguire'>Segui allenamento</button></form>";
				} else {
					$content .= "<form action='dettagli-allenamento.php?pagina=" . $_GET['pagina'] . "&percorso=" . $_GET['percorso'] . "&nomeBreadcrumb=" . $_GET['nomeBreadcrumb'] . " method='post'><input type='hidden' name='id' value='" . $queryOverviewAllenamentoResult[0]['id'] . "' readonly><button type='submit' name='segui' value='nonSeguire'>Smetti di seguire allenamento</button></form>";
				}	
			}

			$content .= '</ul>';

			foreach ($queryDettaglioAllenamentoResult as $esercizio) {
				$content .= '<div class="esercizio">';
				$content .= '<h2>' . $esercizio['nome'] . '</h2>';
				$content .= '<p>' . $esercizio['descrizione'] . '</p>';
				$content .= '<ul>';
				$content .= '<li>' . $esercizio['peso'] . '</li>';
				$content .= '<li>' . $esercizio['serie'] . '</li>';
				$content .= '<li>' . $esercizio['ripetizioni'] . '</li>';
				
				if ($esercizio['durata'] != null) {
					$content .= '<li>' . $esercizio['durata'] . '</li>';
				}
				
				$content .= '</ul>';
				$content .= '</div>';
			}
		} else {
			$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $queryOverviewAllenamentoResult[0]['id']);
			$content .= "<p>Allenamento eliminato!</p>";
		}
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	$paginaHTML = str_replace("<genitoreBreadcrumb/>", "<a href='" . $_GET['percorso'] . ".php?pagina=" . $_GET['pagina'] . "'>" . $_GET['nomeBreadcrumb'] . "</a>", file_get_contents("dettagli-allenamento.html"));
	echo str_replace("<dettaglioAllenamento/>", $content, $paginaHTML);
?>