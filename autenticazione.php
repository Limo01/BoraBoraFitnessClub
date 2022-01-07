<?php
	require_once "db_sample.php";
	use DB\DBAccess;
	session_start();


	function isUsernameCorrect($submitted, $connessione) {
		//TODO: anti injection
		$query = "SELECT username FROM cliente WHERE username = '".$submitted."'";
		$queryResult = $connessione->doReadQuery($query);

		if($queryResult != null) {
			return true;
		} else {
			return false;
		}
	}

	function isPasswordCorrect($name, $password, $connessione) {
		//TODO: anti injection
		$query = "SELECT password FROM cliente WHERE username = '".$name."'";
		$queryResult = $connessione->doReadQuery($query);

		$row = $queryResult;

		if(isset($row[0]["password"]) && $row[0]["password"] == $password) {
			return true;
		} else {
			return false;
		}
	}

	function isEmailValid ($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		else {
			return false;
		}
	}

	function login()
	{
		$paginaHTML = file_get_contents("autenticazione.html");
		
		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();
		
		
		$nomeUtente = $_POST["nomeUtenteAccesso"];
		$password = $_POST["passwordAccesso"];
		$out = "";
		
		//TODO: messaggio unico username o password errata
		if ($connessioneOK) {
			$userOK = isUsernameCorrect($nomeUtente, $connessione);
			$pwOK = isPasswordCorrect($nomeUtente, $password, $connessione);
			$connessione->closeConnection();
			
			if($userOK == true){
				if($pwOK == true){
					$_SESSION["loggedin"] = true;
					$_SESSION["username"] = $nomeUtente;
					header("location: area-personale.php");
				}
				else {
					$out = "password non corretta";
				}
			}
			else {
				$out = "nome utente non corretto";
			}
		} else {
			$out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
		
		echo str_replace("<autenticazione/>", $out, $paginaHTML);
	}
	
	function registration()
	{
		$paginaHTML = file_get_contents("autenticazione.html");
		
		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();
		
		
		$nome = $_POST["nomeRegistrazione"];
		$cognome = $_POST["cognomeRegistrazione"];
		$nascita = $_POST["dataNascitaRegistrazione"];
		$username = $_POST["nomeUtenteRegistrazione"];
		$password1 = $_POST["passwordRegistrazione"];
		$password2 = $_POST["passwordConfermaRegistrazione"];
		$email = $_POST["emailRegistrazione"];
		$tel = $_POST["telRegistrazione"];
		$badge = "123456789";
		$out = "";
		
		/*
			check:
			username non presente nel sistema
			pw1 == pw2
			email valida
			tel valido??
		 */
		if ($connessioneOK) {
			$userDoppio = isUsernameCorrect($username, $connessione);
			$emailValid = isEmailValid($email);

			if($userDoppio == false && $password1 == $password2 && $emailValid){
				$query = "insert into cliente(username, password, nome, cognome, email, data_nascita, badge, entrate, numero_telefono, nome_abbonamento, data_inizio, data_fine)
				values (
					'" . $username . 
					"', '" . $password1 .
					"', '" . $nome .
					"', '" . $cognome .
					"', '" . $email .
					"', '" . $nascita .
					"', '" . $badge .
					"', 0, '" . $tel .
					"', null, null, null)";

					$connessione->doWriteQuery($query);
			} else {
				if($userDoppio){
					$out .= "<p>username non disponibile</p>";
				}
				if($password1 != $password2){
					$out .= "<p>verifica di aver inserito correttamente la password</p>";
				}
				if(!$emailValid){
					$out .= "<p>inserisci una email valida</p>";
				}
			}

			$connessione->closeConnection();

		} else {
			$out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
		
		echo str_replace("<registrazione/>", $out, $paginaHTML);
	}

	if(isset($_POST['loginSubmit'])){
		login();
	} elseif (isset($_POST['registrationSubmit'])){
		registration();
	}

		
?>
