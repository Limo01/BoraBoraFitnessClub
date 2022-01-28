<?php
	require_once "db.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$user = $_SESSION["username"];
	}
	else{
		header("location: ../autenticazione.php?url=compra-abbonamento.php");
		return;
	}

	if(!isset($_SESSION["tipo-abbonamento"])){
		die("Si è verificato un errore nel server");
	}

	$tipo_abbonamento = $_SESSION["tipo-abbonamento"];

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if($connessioneOK){
		
		switch ($tipo_abbonamento) {
			case "singola":
				$connessione->doWriteQuery("UPDATE utente SET entrate=entrate+1	WHERE username=?", "s", $user);
				break;
			case "dieci-entrate":
				$connessione->doWriteQuery("UPDATE utente SET entrate=entrate+10 WHERE username=?", "s", $user);
				break;
			case "mensile":
				$connessione->doWriteQuery("UPDATE utente SET nome_abbonamento='Mensile', data_inizio=CURRENT_DATE(), data_fine=DATE_ADD(CURRENT_DATE(),INTERVAL 1 MONTH) WHERE username=?", "s", $user);
				break;
			case "annuale":
				$connessione->doWriteQuery("UPDATE utente SET nome_abbonamento='Annuale', data_inizio=CURRENT_DATE(), data_fine=DATE_ADD(CURRENT_DATE(),INTERVAL 1 YEAR) WHERE username=?", "s", $user);
				break;
		}

		$connessione->closeConnection();

		header("location: ../area-personale.php?acquisto=1");
		return;
	}

	$connessione->closeConnection();
	die("Si è verificato un errore nel server!");
?>