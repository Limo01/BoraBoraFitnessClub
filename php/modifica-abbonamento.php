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

	if (isset($_GET["update"]) && ($_GET["update"] === "2" || $_GET["update"] === "0")) {
		$update = $_GET["update"];
	} else {
		die("Si è verificato un errore");
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$abbonamento = $_POST["abbonamento"];
			$scadenza = $_POST["scadenza"];
			$entrate = $_POST["entrate"];

			$esito = false;
			//if(isScadenzaValid($scadenza) && isEntrateValid($entrate)) {		TODO: funzioni per il check
				$esito = $connessione->doWriteQuery("UPDATE utente SET nome_abbonamento = ?, data_inizio = CURRENT_DATE(), data_fine = DATE_ADD(CURRENT_DATE(), INTERVAL 1 MONTH) entrate = ? WHERE username = ?", "is", $entrate, $user);
			//}

			$connessione->closeConnection();

			if ($esito) {
				if ($update == 0) {
					header("location: ../modifica-utente.php?usr=" . $user . "&update=1");
				} else {
					header("location: ../modifica-utente.php?usr=" . $user);
				}
				return;
			}
		}
		else {
			$connessione->closeConnection();
		}
	}
	header("location: ../modifica-utente.php?usr=" . $user . "&update=" . $update . "&form_error=1");
?>