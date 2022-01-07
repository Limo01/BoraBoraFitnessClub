<?php
	require_once "db_sample.php";
	use DB1\DBAccess;
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

		if(isset($row[0]["password"]) && password_verify($password,$row[0]["password"])) {
			return true;
		} else {
			return false;
		}
	}

	function isNameValid ($name){
		if (preg_match("/^[a-zA-Z-' àèìòùáéíóú]*$/",$name)){
			return true;
		}
		else {
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

	function getNewBadge ($connessione){
		do{
			$badge = uniqid("BID");
			$query = "select * from cliente where badge = '" . $badge . "'";
			$queryResult = $connessione->doReadQuery($query);
		} while ($queryResult != null);
		return $badge;
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
		$badge = getNewBadge($connessione);
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
			$nomeValid = isNameValid($nome);
			$cognomeValid = isNameValid($cognome);

			if($userDoppio == false && $password1 == $password2 && $nomeValid && $cognomeValid && $emailValid){
				$query = "insert into cliente(username, password, nome, cognome, email, data_nascita, badge, entrate, numero_telefono, nome_abbonamento, data_inizio, data_fine)
				values (
					'" . $username . 
					"', '" . password_hash($password1, PASSWORD_BCRYPT) .
					"', '" . $nome .
					"', '" . $cognome .
					"', '" . $email .
					"', '" . $nascita .
					"', '" . $badge .
					"', 0, '" . $tel .
					"', null, null, null)";

					$connessione->doWriteQuery($query);
			} else {
				if(!$nomeValid){
					$out .= "<p>sono ammesse solamente lettere per il nome</p>";
				}
				if(!$cognomeValid){
					$out .= "<p>sono ammesse solamente lettere per il cognome</p>";
				}
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
