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

	if(isset($_GET['url'])){
		$_SESSION['previousPage'] = $_GET['url'];
	}
	if(isset($_POST['loginSubmit'])){
		login();
	} else {
		$paginaHTML = file_get_contents("html/autenticazione.html");
		$paginaHTML = str_replace("<today_min16anni />", date('Y-m-d', strtotime('-16 years')), $paginaHTML);
		$paginaHTML = str_replace("<today_max110anni />", date('Y-m-d', strtotime('-110 years')), $paginaHTML);
		$paginaHTML = str_replace("<erroreLogin />","",$paginaHTML);
		echo $paginaHTML;
	}

		
?>
