<?php
	$newURL = "https://it.wikipedia.org/wiki/Ciao";
	header('Location: '.$newURL);
	/*require_once "db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("schede-allenamento.html");
	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$startRow = (int)$_GET['pagina'] * 10 - 10;
	$queryResult = "";
	$content = "";

	$query = "SELECT nome, COUNT(id) AS Followers, descrizione, allenamento.username_cliente, data_creazione FROM cliente_allenamento join allenamento on id_allenamento = id GROUP BY id ORDER BY Followers DESC LIMIT " . $startRow . ", 10";

	if ($connessioneOK) {
		$queryResult = $connessione->doQuery($query);
		$connessione->closeConnection();

		$content = '<dl>';

		foreach ($queryResult as $row) {
			$content .= '<dd>' . $row['nome'] . '</dd>';
			$content .= '<dd>' . $row['Followers'] . '</dd>';
			$content .= '<dd>' . $row['descrizione'] . '</dd>';
			$content .= '<dd>' . $row['username_cliente'] . '</dd>';
			$content .= '<dd>' . $row['data_creazione'] . '</dd>';
		}

		$content .= '</dl>';
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo str_replace("<allenamenti/>", $content, $paginaHTML);/*
?>