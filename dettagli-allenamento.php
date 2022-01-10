<?php
	require_once "db.php";
	use DB\DBAccess;

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		$id = $_GET['id'];
		$queryOverviewAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, username_utente, data_creazione FROM allenamento WHERE id = ?", "i", $id);
		$queryNumeroFollowersResult = $connessione->doReadQuery("SELECT COUNT(*) AS Followers FROM allenamento_esercizio WHERE id_allenamento = ?", "i", $id);
		$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = ?", "i", $id);
		
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
			$queryIsClientResult = $connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"]);
			$connection_statement = ($queryIsClientResult[0])? 1 : 0;
		} else {
			$connection_statement = 2;
		}
		
		$connessione->closeConnection();

		$content = '<h2>' . $queryOverviewAllenamentoResult[0]['nome'] . '</h2>';
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
		$content .= '<li>' . $queryNumeroFollowersResult[0]['Followers'] . '</li>';
		$content .= '</ul>';
		$content .= '<ul>';

		if ($connection_statement == 2) { //cliente
			$content .= "<form action='gestioneAllenamenti.php' method='post'><input type='hidden' name='segui' value='ciao'><input type='submit' name='action' value='Segui'/></form>";
		} elseif ($connection_statement == 1) { //admin
			$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&percorso=" . $_GET['percorso'] . "&nomeBreadcrumb=" . $_GET['nomeBreadcrumb'] . "&pagina=" . $_GET['pagina'] . "' method='post'><button type='submit' onclick='elimina()'>Elimina</button></form>";
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
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	$paginaHTML = str_replace("<genitoreBreadcrumb/>", "<a href='" . $_GET['percorso'] . ".php?pagina=" . $_GET['pagina'] . "'>" . $_GET['nomeBreadcrumb'] . "</a>", file_get_contents("dettagli-allenamento.html"));
	echo str_replace("<dettaglioAllenamento/>", $content, $paginaHTML);
?>