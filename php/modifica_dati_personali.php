<?php
	require_once "php/db.php";
	require_once "php/controlli_input.php";
	use DB\DBAccess;

	session_start();

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK && isset($_SESSION["loggedin"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		$user= $_SESSION["username"];
		$nome = $_POST["nome"];
		$cognome = $_POST["cognome"];
		$email = $_POST["email"];
		$data_nascita = $_POST["data_nascita"];
		$telefono = $_POST["telefono"];

		$esito= false;

		if(isNameValid($nome) and isNameValid($cognome) and isEmailValid($email) and isDateValid($data_nascita) and isPhoneNumberValid($telefono)){
			$esito= $connessione->doWriteQuery("UPDATE utente SET nome=?, cognome=?, email=?, data_nascita=?, numero_telefono=?
				WHERE username=?", "ssssss", 
				$nome, $cognome, $email, $data_nascita, $telefono, $user);
		}
		
		$connessione->closeConnection();

		if($esito){
			header("location: area-personale.php");	
			return;	
		}
	}
	header("location: area-personale.php?update=1&form_error=1");
?>