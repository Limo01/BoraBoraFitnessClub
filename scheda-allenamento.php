<?php
	require_once "db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("schede-allenamento.html");
	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$numeroAllenamentiPerPagina = 1;
	$pagina = (int)$_GET['pagina'];
	$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;

	$queryAllenamentiResult = "";
	$queryPagineResult = "";
	$content = "";

	$query = "SELECT id, nome, COUNT(id) AS Followers, descrizione, allenamento.username_cliente, data_creazione FROM cliente_allenamento join allenamento on id_allenamento = id GROUP BY id ORDER BY Followers DESC LIMIT " . $startRow . ", " . $numeroAllenamentiPerPagina;
	$queryPagine = "SELECT COUNT(*) AS numeroAllenamenti FROM allenamento";

	if ($connessioneOK) {
		$queryAllenamentiResult = $connessione->doReadQuery($query);
		$queryPagineResult = $connessione->doReadQuery($queryPagine);
		$connessione->closeConnection();

		$content = '<dl>';
		foreach ($queryAllenamentiResult as $row) {
			$content .= '<dd>' . $row['nome'] . '</dd>';
			$content .= '<dd>' . $row['Followers'] . '</dd>';
			$content .= '<dd>' . $row['descrizione'] . '</dd>';
			$content .= '<dd>' . $row['username_cliente'] . '</dd>';
			$content .= '<dd>' . $row['data_creazione'] . '</dd>';
			$content .= '<dd><a href="dettaglio-allenamento.php?id=' . $row['id'] . '&percorso=scheda-allenamento&nomeBreadcrumb=Allenamenti&pagina=' . $pagina . '">Apri nel dettaglio</a></dd>';
		}
		$content .= '</dl>';

		foreach ($queryPagineResult as $row) {
			$numeroPagine = ceil((int)$row['numeroAllenamenti'] / $numeroAllenamentiPerPagina);
		}

		for ($i = 1; $i < $pagina; $i++) {
	    	$content .= "<a href='scheda-allenamento.php?pagina=" . $i . "'>" . $i . "</a>";
		}

		$content .= "<p>" . $pagina . "</p>";

		for ($i = $pagina + 1; $i <= $numeroPagine; $i++) {
	    	$content .= "<a href='scheda-allenamento.php?pagina=" . $i . "'>" . $i . "</a>";
		}

	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo str_replace("<allenamenti/>", $content, $paginaHTML);
?>