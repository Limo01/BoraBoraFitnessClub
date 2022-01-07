<?php
	require_once "db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("area-personale.html");

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$user = "user";

	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM cliente WHERE username='". $user ."';");
		$connessione->closeConnection();

		$datiPersonali = $result[0];
		
		$paginaHTML= str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
		$paginaHTML= str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
		$paginaHTML= str_replace("<email />", $datiPersonali["email"], $paginaHTML);
		$paginaHTML= str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
		$paginaHTML= str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);
		$paginaHTML= str_replace("<badge />", $datiPersonali["badge"], $paginaHTML);
	} else {
		$listaClienti = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>