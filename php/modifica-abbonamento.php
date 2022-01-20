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
			$nome = $_POST["nome"];
			$cognome = $_POST["cognome"];
			$email = $_POST["email"];
			$data_nascita = $_POST["data_nascita"];
			$telefono = $_POST["telefono"];

			$esito = false;

			if(isNameValid($nome) && isNameValid($cognome) && isEmailValid($email) && isDateValid($data_nascita) && isPhoneNumberValid($telefono)){
				$esito= $connessione->doWriteQuery("UPDATE utente SET nome=?, cognome=?, email=?, data_nascita=?, numero_telefono=?
					WHERE username=?", "ssssss", 
					$nome, $cognome, $email, $data_nascita, $telefono, $user);
			}

			$connessione->closeConnection();

			if ($esito) {
				if ($update == 0) {
					header("location: ../" . ($hasUsr ? "modifica-utente.php?usr=" . $user . "&" : "area-personale.php?") . "update=2");
				} else {
					header("location: ../" . ($hasUsr ? "modifica-utente.php?usr=" . $user : "area-personale.php"));
				}
				return;
			}
		}
		else {
			$connessione->closeConnection();
		}
	}
	header("location: ../" . ($hasUsr ? "modifica-utente.php?usr=" . $user . "&" : "area-personale.php?") . "update=" . $update . "&form_error=1");
?>