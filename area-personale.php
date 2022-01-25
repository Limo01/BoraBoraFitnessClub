<?php
	require_once "php/db.php";
	require_once "php/funzioni-area-personale.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$user = $_SESSION["username"];
		$admin = $_SESSION["isAdmin"];
	}
	else{
		header("Location: autenticazione.php");
		return;
	}

	$updatePersonalData = false;
	if(isset($_GET["update"]) && $_GET["update"] == 1) {
		$updatePersonalData = true;
	}
	
	$formError = false;
	if(isset($_GET["form_error"]) && $_GET["form_error"] == 1) {
		$formError = true;
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM utente WHERE username = ?", "s", $user);
		if ($result == null) {
			header("Location: autenticazione.php");
			die("Errore: utente inesistente");
		}
		$datiPersonali = $result[0];

		if ($admin) {
			if (isset($_POST["elimina"])) {
				$connessione->doWriteQuery("DELETE FROM utente WHERE username = ?", "s", $_POST["user"]);
				$userRemoved = true;
			} else {
				$userRemoved = false;
			}

			$utenti = $connessione->doReadQuery("SELECT username FROM utente WHERE username != ? and is_admin = 0", "s", $user);
		}

		$ultimoIngresso = $connessione->doReadQuery("SELECT dataora_entrata FROM accesso WHERE username_utente=? order by dataora_entrata DESC limit 1", "s", $user);

		if($ultimoIngresso != null){
			$ultimoIngresso = $ultimoIngresso[0];
 		}

		$schedeSeguite = $connessione->doReadQuery("SELECT id, nome, descrizione FROM utente_allenamento JOIN allenamento ON (utente_allenamento.id_allenamento=allenamento.id) WHERE utente_allenamento.username_utente=? AND utente_allenamento.username_utente!=allenamento.username_utente", "s", $user);

		$schedeCreate = $connessione->doReadQuery("SELECT id, nome, descrizione FROM allenamento WHERE allenamento.username_utente=?", "s", $user);

		$connessione->closeConnection();

		$paginaHTML = initPage($admin);

		$paginaHTML = replaceDatiPersonali($datiPersonali, $updatePersonalData, $paginaHTML);
		$paginaHTML = replaceDatiAbbonamento($datiPersonali, $paginaHTML);
		unset($datiPersonali);

		$paginaHTML = replaceUltimoIngresso($ultimoIngresso, $paginaHTML);
		unset($ultimoIngresso);

		$paginaHTML = replaceSchedeAllenamento($schedeSeguite, $schedeCreate, $admin, $paginaHTML);
		unset($schedeSeguite, $schedeCreate);

		if ($admin) {
			$paginaHTML = replaceGestioneUtenti($utenti, $userRemoved, $paginaHTML);
			unset($utenti);
		}
	} else {
		$paginaHTML = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>