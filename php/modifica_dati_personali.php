<?php
	require_once "db.php";
	require_once "controlli_input.php";
	use DB\DBAccess;

	session_start();

	if (isset($_GET["update"]) && ($_GET["update"] == 1 || $_GET["update"] == 0)) {
		$update = $_GET["update"];
	} else {
		die("Si è verificato un errore");
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		if (isset($_SESSION["loggedin"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
			$user = $_SESSION["username"];
			$admin = $user;
			$hasUsr = false;
			if ($_SESSION["isAdmin"] && isset($_GET["usr"])) {
				$hasUsr = true;
				$user = $_GET["usr"];
			}

			$nome = $_POST["nome"];
			$cognome = $_POST["cognome"];
			$email = $_POST["email"];
			$data_nascita = $_POST["dataNascita"];
			$telefono = $_POST["telefono"];

			$esito = false;

			if(isNameValid($nome) && isNameValid($cognome) && isEmailValid($email) && isDateValid($data_nascita, $_SESSION["isAdmin"]) && isPhoneNumberValid($telefono)){
				$esito= $connessione->doWriteQuery("UPDATE utente SET nome=?, cognome=?, email=?, data_nascita=?, numero_telefono=?
					WHERE username=?", "ssssss", 
					$nome, $cognome, $email, $data_nascita, $telefono, $user);
			}

			$connessione->closeConnection();

			if ($esito) {
				if ($update == 0) {
					header("location: ../" . ($hasUsr ? "visualizza-utente.php?usr=" . $user . "&" : "area-personale.php?") . "update=2");
				} else {
					header("location: ../" . ($hasUsr ? "visualizza-utente.php?usr=" . $user : "area-personale.php"));
				}
				return;
			}
		}
		else {
			$connessione->closeConnection();
		}
	}
	header("location: ../" . ($hasUsr ? "visualizza-utente.php?usr=" . $user . "&" : "area-personale.php?") . "update=" . $update . "&form_error=1");
	return;
?>