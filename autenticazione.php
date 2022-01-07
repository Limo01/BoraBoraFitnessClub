<?php
	require_once "db.php";
	use DB\DBAccess;
	session_start();

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
			$userOK = $connessione->isUsernameCorrect($nomeUtente);
			$pwOK = $connessione->isPasswordCorrect($nomeUtente,$password);
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
		
		
		$nomeUtente = $_POST["nomeUtenteAccesso"];
		$password = $_POST["passwordAccesso"];
		$out = "";
		
		//TODO: messaggio unico username o password errata
		if ($connessioneOK) {
			$userOK = $connessione->isUsernameCorrect($nomeUtente);
			$pwOK = $connessione->isPasswordCorrect($nomeUtente,$password);
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

	if(isset($_POST['loginSubmit'])){
		login();
	} elseif (isset($_POST['registrationSubmit'])){
		registration();
	}

		
?>
