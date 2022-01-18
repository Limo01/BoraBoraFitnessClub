<?php
	require_once "db.php";
	require_once "controlli_input.php";
	use DB\DBAccess;

	session_start();

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		if (isset($_SESSION["loggedin"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
			$user = $_SESSION["username"];
			$hasUsr = false;
			if (
				isset($_GET["usr"]) &&
				$connessione->doReadQuery("SELECT is_admin FROM utente WHERE username=?", "s", $admin)[0]["is_admin"]
			) {
				$hasUsr = true;
				$user = $_GET["usr"];
			}

			$nome = $_POST["nome"];
			$cognome = $_POST["cognome"];
			$email = $_POST["email"];
			$data_nascita = $_POST["data_nascita"];
			$telefono = $_POST["telefono"];

			$esito = false;

			if(isNameValid($nome) and isNameValid($cognome) and isEmailValid($email) and isDateValid($data_nascita) and isPhoneNumberValid($telefono)){
				$esito= $connessione->doWriteQuery("UPDATE utente SET nome=?, cognome=?, email=?, data_nascita=?, numero_telefono=?
					WHERE username=?", "ssssss", 
					$nome, $cognome, $email, $data_nascita, $telefono, $user);
			}

			$connessione->closeConnection();

			if ($esito) {
				header("location: ../" . ($hasUsr ? "modifica-utente.php?usr=" . $user : "area-personale.php"));
				return;
			}
		}
		else {
			$connessione->closeConnection();
		}
	}
	header("location: ../" . ($hasUsr ? "modifica-utente.php?usr=" . $user . "&" : "area-personale.php?") . "update=1&form_error=1");
?>