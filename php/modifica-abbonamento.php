<?php
	require_once "db.php";
	require_once "controlli_input.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$admin = $_SESSION["username"];
	} else{
		header("location: autenticazione.php");
		die("Errore: il redirect è stato disabilitato");
	}

	if (!$_SESSION["isAdmin"]) {
		header("location: area-personale.php");
		die("Accesso negato!");
	}

	if (isset($_GET["usr"])) {
		$user = $_GET["usr"];
	} else {
		die("Errore: nessun utente selezionato");
	}

	if ($admin == $user) {
		header("location: area-personale.php");
		die("Errore: il redirect è stato disabilitato");
	}

	if (isset($_GET["update"]) && ($_GET["update"] == 2 || $_GET["update"] == 0)) {
		$update = $_GET["update"];
	} else {
		die("Si è verificato un errore");
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$abbonamento = $_POST["abbonamento"];
			if ($abbonamento != "Nessuno") {
				$scadenza = $_POST["scadenza"];
			}
			$entrate = $_POST["entrate"];

			$esito = false;
			//if(isScadenzaValid($scadenza) && isEntrateValid($entrate)) {		TODO: funzioni per il check
				if ($abbonamento != "Nessuno") {
					$esito = $connessione->doWriteQuery("UPDATE utente SET nome_abbonamento = ?, data_fine = ?, entrate = ? WHERE username = ?", "ssis", $abbonamento, $scadenza, $entrate, $user);
				} else {
					$esito = $connessione->doWriteQuery("UPDATE utente SET nome_abbonamento = NULL, data_fine = NULL, entrate = ? WHERE username = ?", "is", $entrate, $user);
				}
			//}	
			$connessione->closeConnection();

			if ($esito) {
				if ($update == 0) {
					header("location: ../visualizza-utente.php?usr=" . $user . "&update=1");
				} else {
					header("location: ../visualizza-utente.php?usr=" . $user);
				}
				return;
			}
		}
		else {
			$connessione->closeConnection();
		}
	}
	header("location: ../visualizza-utente.php?usr=" . $user . "&update=" . $update . "&form_error=1");
?>