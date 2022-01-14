<?php
	require_once "db.php";
	use DB\DBAccess;

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$numeroPagine = 0;

	if ($connessioneOK) {
		$numeroAllenamentiPerPagina = 5;
		$pagina = $_GET['pagina'];
		$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;

		$queryAllenamentiResult = $connessione->doReadQuery("SELECT id, nome, descrizione, allenamento.username_utente, data_creazione, COUNT(id) AS Followers FROM allenamento LEFT JOIN utente_allenamento ON id = id_allenamento GROUP BY id ORDER BY Followers DESC LIMIT ?, ?", "ii", $startRow, $numeroAllenamentiPerPagina);
		$queryPagineResult = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento");

		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
			$queryIsClientResult = $connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"]);
			$connection_statement = ($queryIsClientResult[0])? 1 : 0;
		} else {
			$connection_statement = 2;
		}

		$content = "<form action='' method='post'><input type='hidden' name='isAdmin' value='" . $connection_statement . "' readonly><button type='submit'>Crea allenamento</button></form>";

		foreach ($queryAllenamentiResult as $row) {
			if (!(isset($_POST['elimina']) && $_POST['id'] == $row['id'])) {
				$content .= '<div class="allenamento">';
				$content .= '<h2>' . $row['nome'] . '</h2>';
				$content .= '<p>' . $row['descrizione'] . '</p>';

				$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = ?", "i", $row['id']);
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
				$content .= '<li>' . $row['username_utente'] . '</li>';
				$content .= '<li>' . $row['data_creazione'] . '</li>';
				$content .= '<li>' . ($row['Followers'] == null ? 0 : $row['Followers']) . '</li>';
				$content .= '</ul>';
				$content .= '<ul>';
				$content .= '<li>';
				$content .= '<a href="dettagli-allenamento.php?id=' . $row['id'] . '&percorso=allenamenti&nomeBreadcrumb=Allenamenti&pagina=' . $pagina . '">Apri nel dettaglio</a>';
				
				if ($connection_statement == 2 || ($connection_statement == 0 && $row['username_utente'] == $_SESSION["username"])) { //admin: 1
					$content .= "<form action='' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly><input type='hidden' name='isAdmin' value='" . $connection_statement . "' readonly><button type='submit'>Modifica allenamento</button></form>";
					$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly><button type='submit' name='elimina'>Elimina allenamento</button></form>";
				} //else...
				if ($connection_statement == 2) { //cliente: 0
					$segui = true;
					if (isset($_POST['segui']) && $_POST['id'] == $row['id']) {
						if ($_POST['segui'] == "seguire") {
							$connessione->doWriteQuery("INSERT INTO utente_allenamento(username_utente, id_allenamento) VALUES (?, ?)", "si", "admin", $row['id']);
							$segui = false;
							$content .= "<p>Hai iniziato a seguire questo allenamento!</p>";
						} else {
							$connessione->doWriteQuery("DELETE FROM utente_allenamento WHERE id = ?", "i", $row['id']);
							$segui = true;
							$content .= "<p>Hai smesso di seguire questo allenamento!</p>";
						}
					}

					if ($segui) {
						$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly><button type='submit' name='segui' value='seguire'>Segui allenamento</button></form>";
					} else {
						$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly><button type='submit' name='segui' value='nonSeguire'>Smetti di seguire allenamento</button></form>";
					}	
				}
				$content .= '</li>';
				$content .= '</div>';
			} else {
				$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $row['id']);
				$content .= "<p>Allenamento eliminato!</p>";
			}
		}
		$connessione->closeConnection();

		$numeroPagine = ceil((int)$queryPagineResult[0]['numeroAllenamenti'] / $numeroAllenamentiPerPagina);
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	$contentPagine = "";

	for ($i = 1; $i < $pagina; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}

	$contentPagine .= "<li>" . $pagina . "</li>";

	for ($i = $pagina + 1; $i <= $numeroPagine; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}

	$paginaHTML = str_replace("<allenamenti/>", $content, file_get_contents("allenamenti.html"));
	echo str_replace("<pagine/>", $contentPagine, $paginaHTML);
?>