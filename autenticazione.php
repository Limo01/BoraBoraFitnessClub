<?php
	require_once "php/db.php";
	require_once "php/controlli_input.php";
	use DB\DBAccess;
	session_start();

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
				$out = "<p class=\"errore_form\">Username o password errata, ritenta il login!</p>";
			}
		} else {
			$out = "<p class=\"errore_form\">I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
		
		echo str_replace("<erroreLogin />", $out, $paginaHTML);
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
			$telValid = isPhoneNumberValid($tel);

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
					$paginaHTML = str_replace("<erroreNome />","Il nome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				} else {
					$paginaHTML = str_replace("<erroreNome />","",$paginaHTML);
					$paginaHTML = str_replace("id=\"nome\"","id=\"nome\" value=\"" . $nome . "\"", $paginaHTML);
				}

				if(!$cognomeValid){
					$paginaHTML = str_replace("<erroreCognome />","Il cognome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				} else {
					$paginaHTML = str_replace("<erroreCognome />","",$paginaHTML);
					$paginaHTML = str_replace("id=\"cognome\"","id=\"cognome\" value=\"" . $cognome . "\"", $paginaHTML);
				}

				if($userDoppio){
					$paginaHTML = str_replace("<erroreUsername />","Lo username inserito non è disponibile.",$paginaHTML);
				} elseif(!$userValid){
					$paginaHTML = str_replace("<erroreUsername />","Lo username inserito non è valido. Deve contenere solo lettere e numeri, senza caratteri speciali (? , * ; + .).",$paginaHTML);
				} else {
					$paginaHTML = str_replace("<erroreUsername />","",$paginaHTML);
					$paginaHTML = str_replace("id=\"username\"","id=\"username\" value=\"" . $username . "\"", $paginaHTML);
				}

				if($password1 != $password2){
					$paginaHTML = str_replace("<erroreConfermaPassword />","errore nell'inserimento della password",$paginaHTML);
				}
				else {
					$paginaHTML = str_replace("<erroreConfermaPassword />","",$paginaHTML);
				}

				if(!$emailValid){
					$paginaHTML = str_replace("<erroreEmail />","L'email inserita non è valida.",$paginaHTML);
				} else {
					$paginaHTML = str_replace("<erroreEmail />","",$paginaHTML);
					$paginaHTML = str_replace("id=\"email\"","id=\"email\" value=\"" . $email . "\"", $paginaHTML);
				}

				if(!$telValid){
					$out .= "<p>inserisci un numero di telefono valido</p>";
					$paginaHTML = str_replace("<erroreTelefono />","Il numero di telefono inserito non è valida.",$paginaHTML);
				} else {
					$paginaHTML = str_replace("<erroreTelefono />","",$paginaHTML);
					$paginaHTML = str_replace("id=\"telefono\"","id=\"telefono\" value=\"" . $tel . "\"", $paginaHTML);
				}

				//da cambiare se ci sono controlli per la data
				$paginaHTML = str_replace("id=\"dataNascita\"","id=\"dataNascita\" value=\"" . $nascita . "\"", $paginaHTML);
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
		$paginaHTML = file_get_contents("html/autenticazione.html");
		$paginaHTML = str_replace("<today_min16anni />", date('Y-m-d', strtotime('-16 years')), $paginaHTML);
		$paginaHTML = str_replace("<today_max110anni />", date('Y-m-d', strtotime('-110 years')), $paginaHTML);
		echo $paginaHTML;
	}

		
?>
