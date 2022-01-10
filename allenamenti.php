<?php
	require_once "db.php";
	use DB\DBAccess;

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		$numeroAllenamentiPerPagina = 1;
		$pagina = $_GET['pagina'];
		$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;

		$queryAllenamentiResult = $connessione->doReadQuery("SELECT id, nome, COUNT(id) AS Followers, descrizione, allenamento.username_utente, data_creazione FROM utente_allenamento join allenamento on id_allenamento = id GROUP BY id ORDER BY Followers DESC LIMIT ?, ?", "ii", $startRow, $numeroAllenamentiPerPagina);
		$queryPagineResult = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento");

		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
			$queryIsClientResult = $connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"]);
			$connection_statement = ($queryIsClientResult[0])? 1 : 0;
		} else {
			$connection_statement = 2;
		}

		foreach ($queryAllenamentiResult as $row) {
			$content = '<div class="allenamento">';
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

			$connessione->closeConnection();

			$content .= '<ul>';
			$content .= '<li>' . $row['username_utente'] . '</li>';
			$content .= '<li>' . $row['data_creazione'] . '</li>';
			$content .= '<li>' . $row['Followers'] . '</li>';
			$content .= '</ul>';
			$content .= '<ul>';
			$content .= '<li>';
			$content .= '<a href="dettagli-allenamento.php?id=' . $row['id'] . '&percorso=allenamenti&nomeBreadcrumb=Allenamenti&pagina=' . $pagina . '">Apri nel dettaglio</a>';
			
			if ($connection_statement == 0) { //cliente
				$content .= "<br>BOTTONE SEGUI<br>";
			} elseif ($connection_statement == 1) { //admin
				$content .= "<br>BOTTONE ELIMINA<br>";
			}

			$content .= '</li>';
			$content .= '</div>';
		}

		$numeroPagine = ceil((int)$queryPagineResult[0]['numeroAllenamenti'] / $numeroAllenamentiPerPagina);
		$contentPagine = "";

		for ($i = 1; $i < $pagina; $i++) {
	    	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
		}

		$contentPagine .= "<li>" . $pagina . "</li>";

		for ($i = $pagina + 1; $i <= $numeroPagine; $i++) {
	    	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
		}

		if (isset($_POST['name'])) {
			$content .= $_POST['name'];
		}
		if (isset($_POST['email'])) {
			$content .= $_POST['email'];
		}
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}


	$paginaHTML = str_replace("<allenamenti/>", $content, file_get_contents("allenamenti.html"));
	echo str_replace("<pagine/>", $contentPagine, $paginaHTML);
?>