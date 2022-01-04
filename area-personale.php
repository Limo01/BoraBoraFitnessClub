<?php
	require_once "db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("area-personale.html");

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();


	$listaClienti = "";
	$clienti = "";

	if ($connessioneOK) {
		$clienti = $connessione->getClienti();
		$connessione->closeConnection();
		if ($clienti != null) {
			$listaClienti = '<dl>';
			foreach ($clienti as $cliente) {
				$listaClienti .= '<dd>' . $cliente['username'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['password'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['nome'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['cognome'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['email'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['data_nascita'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['badge'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['entrate'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['numero_telefono'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['nome_abbonamento'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['data_inizio'] . '</dd>';
				$listaClienti .= '<dd>' . $cliente['data_fine'] . '</dd>';
			}
			$listaClienti .= '</dl>';
		} else {
			$listaClienti = "<p>Non ci sono informazioni relative ai clienti!</p>";
		}
	} else {
		$listaClienti = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo str_replace("<areaPersonale/>", $listaClienti, $paginaHTML);
?>