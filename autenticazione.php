<?php
	require_once "php/db.php";
	use DB\DBAccess;
	session_start();

	function isUsernameCorrect($submitted, $connessione) {
		$queryResult = $connessione->doReadQuery("SELECT username FROM utente WHERE username = ?", "s", $submitted);

		if($queryResult != null) {
			return true;
		} else {
			return false;
		}
	}

	function isPasswordCorrect($name, $password, $connessione) {
		$queryResult = $connessione->doReadQuery("SELECT password FROM utente WHERE username = ?", "s", $name);

		$row = $queryResult;

		if(isset($row[0]["password"]) && password_verify($password,$row[0]["password"])) {
			return true;
		} else {
			return false;
		}
	}

	function isAdmin($user, $connessione) {
		$result = $connessione->doReadQuery("SELECT is_admin FROM utente WHERE username = ?", "s", $user);
		if ($result != null && $result[0]["is_admin"] == 1) {
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

	function isUsernameValid ($name){
		if (preg_match("/^[0-9a-zA-Z-' àèìòùáéíóú]*$/",$name)){
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

	function isTelValid ($tel){
		if (preg_match("/^\+[0-9]+$|^[0-9]+$/",$tel)){
			return true;
		}
		else {
			return false;
		}
	}

	function getNewBadge ($connessione){
		do{
			$badge = uniqid("BID");
			$query = "select * from utente where badge = '" . $badge . "'";
			$queryResult = $connessione->doReadQuery($query);
		} while ($queryResult != null);
		return $badge;
	}

	function login()
	{
		$paginaHTML = file_get_contents("html/autenticazione.html");
		
		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();
		
		
		$nomeUtente = $_POST["nomeUtenteAccesso"];
		$password = $_POST["passwordAccesso"];
		$out = "";
		
		//TODO: messaggio unico username o password errata
		if ($connessioneOK) {
			$userOK = isUsernameCorrect($nomeUtente, $connessione);
			$pwOK = isPasswordCorrect($nomeUtente, $password, $connessione);
			$isAdmin = isAdmin($nomeUtente, $connessione);
			$connessione->closeConnection();
			
			if($userOK == true && $pwOK == true){
				$_SESSION["loggedin"] = true;
				$_SESSION["username"] = $nomeUtente;
				$_SESSION["isAdmin"] = $isAdmin;
				
				if(isset($_SESSION['previousPage'])){
					$previous = $_SESSION['previousPage'];
					unset($_SESSION['previousPage']);
					header("location: " . $previous);
				} else{
					header("location: area-personale.php");
				}
			}
			else {
				$out = "nome utente o password errato";
			}
		} else {
			$out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
		
		echo str_replace("<autenticazione/>", $out, $paginaHTML);
	}
	
	function registration()
	{
		$paginaHTML = file_get_contents("html/autenticazione.html");
		
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
		
		if ($connessioneOK) {
			$userDoppio = isUsernameCorrect($username, $connessione);
			$userValid = isUsernameValid($username);
			$emailValid = isEmailValid($email);
			$nomeValid = isNameValid($nome);
			$cognomeValid = isNameValid($cognome);
			$telValid = isTelValid($tel);

			if(!$userDoppio && $userValid && $password1 == $password2 && $nomeValid && $cognomeValid && $emailValid && $telValid){
				$connessione->doWriteQuery("INSERT INTO utente(username, password, nome, cognome, email, data_nascita, badge, entrate, numero_telefono, nome_abbonamento, data_inizio, data_fine)
					VALUES(?,?,?,?,?,?,?,0,?,null,null,null)", "ssssssss",
					$username, password_hash($password1, PASSWORD_BCRYPT), $nome, $cognome, $email, $nascita, $badge, $tel);
					$_SESSION["loggedin"] = true;
					$_SESSION["username"] = $username;
					$_SESSION["isAdmin"] = false;
					
					if(isset($_SESSION['previousPage'])){
						$previous = $_SESSION['previousPage'];
						unset($_SESSION['previousPage']);
						header("location: " . $previous);
					} else{
						header("location: area-personale.php");
					}
			} else {
				if(!$nomeValid){
					// $out .= "<p>sono ammesse solamente lettere per il nome</p>";
					$paginaHTML = str_replace("<erroreNome />","Il nome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				}
				if(!$cognomeValid){
					// $out .= "<p>sono ammesse solamente lettere per il cognome</p>";
					$paginaHTML = str_replace("<erroreCognome />","Il cognome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				}
				if($userDoppio){
					// $out .= "<p>username non disponibile</p>";
					$paginaHTML = str_replace("<erroreUsername />","Lo username inserito non è disponibile.",$paginaHTML);
				}
				if(!$userValid){
					// $out .= "<p>sono ammesse solamente lettere e numeri per lo username</p>";
					$paginaHTML = str_replace("<erroreUsername />","Lo username inserito non è valido. Deve contenere solo lettere e numeri, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				}
				if($password1 != $password2){
					$out .= "<p>verifica di aver inserito correttamente la password</p>";
				}
				if(!$emailValid){
					#$out .= "<p>inserisci una email valida</p>";
					$paginaHTML = str_replace("<erroreEmail />","L'email inserita non è valida.",$paginaHTML);
				}
				if(!$telValid){
					$out .= "<p>inserisci un numero di telefono valido</p>";
					$paginaHTML = str_replace("<erroreTelefono />","Il numero di telefono inserito non è valida.",$paginaHTML);
				}
			}

			$connessione->closeConnection();

		} else {
			$out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
		
		echo str_replace("<registrazione />", $out, $paginaHTML);
	}

	if(isset($_GET['url'])){
		$_SESSION['previousPage'] = $_GET['url'];
	}
	if(isset($_POST['loginSubmit'])){
		login();
	} elseif (isset($_POST['registrationSubmit'])){
		registration();
	} else {
		echo file_get_contents("html/autenticazione.html");
	}

		
?>
