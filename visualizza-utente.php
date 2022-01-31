<?php
	require_once "php/db.php";
	require_once "php/funzioni-area-personale.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$admin = $_SESSION["username"];
	} else{
		header("Location: autenticazione.php");
		die("Errore: il redirect è stato disabilitato");
	}

	if (!$_SESSION["isAdmin"]) {
		header("Location: area-personale.php");
		die("Accesso negato!");
	}

	if (isset($_GET["usr"])) {
		$user = $_GET["usr"];
	} else {
		header("Location: 404.html");
		die("Errore: pagina non trovata");
	}

	if ($admin == $user) {
		header("Location: area-personale.php");
		die("Errore: il redirect è stato disabilitato");
	}

	$paginaHTML = file_get_contents("html/area-personale.html");
	$paginaHTML = str_replace("<logout />", "", $paginaHTML);
	$paginaHTML = str_replace("<breadcrumb />", "<a href='area-personale.php' aria-label='Area personale' title='Area personale'>Area personale [admin]</a> &gt;&gt; Area personale (<username />)", $paginaHTML);
	$paginaHTML = str_replace("<admin />", "", $paginaHTML);
	$paginaHTML = str_replace("<gestione_utenti />", "", $paginaHTML);
	$paginaHTML = str_replace("<widget />", "widget_area_personale", $paginaHTML);
	$paginaHTML = str_replace("<link_menu />", '<a href="area-personale.php" aria-label="Area personale"
						title="Area personale">Area personale</a>', $paginaHTML);
	
	$update = -1;
	$updatePersonalData = false;
	$updateSubscription = false;
	if(isset($_GET["update"])) {
		$update = $_GET["update"];
		if ($update == 1) {
			$updatePersonalData = true;
		} elseif ($update == 2) {
			$updateSubscription = true;
		} elseif ($update == 0) {
			$updatePersonalData = true;
			$updateSubscription = true;
		}
	}
	
	$formError = 0;
	if(isset($_GET["form_error"])){
		$formError = $_GET["form_error"];
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	
	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM utente WHERE username=? and is_admin=0", "s", $user);
		if ($result == null) {
			$connessione->closeConnection();
			header("Location: 404.html");
			die("Errore: pagina non trovata");
		}
		$datiPersonali = $result[0];

		$ultimoIngresso = $connessione->doReadQuery("SELECT dataora_entrata FROM accesso WHERE username_utente=? order by dataora_entrata DESC limit 1", "s", $user);

		if($ultimoIngresso!=null){
			$ultimoIngresso = $ultimoIngresso[0];
 		}

		$schedeSeguite = $connessione->doReadQuery("SELECT id, nome, descrizione FROM utente_allenamento JOIN allenamento ON (utente_allenamento.id_allenamento=allenamento.id) WHERE utente_allenamento.username_utente=? AND utente_allenamento.username_utente!=allenamento.username_utente", "s", $user);

		$schedeCreate = $connessione->doReadQuery("SELECT id, nome, descrizione FROM allenamento WHERE allenamento.username_utente=?", "s", $user);

		if ($updateSubscription) {
			$abbonamenti = $connessione->doReadQuery("SELECT nome FROM abbonamento");
		}

		$connessione->closeConnection();

		//Informazioni personali
		if(!$updatePersonalData){
			$button = '<a href="visualizza-utente.php?usr=<username />&update=<update />#dati_personali_widget">Modifica</a>';
			$personalData = str_replace("<update />", ($update > 0 ? 0 : 1), file_get_contents("html/dati_personali.html") . $button);
		}
		else{
			$personalData = "";
			
			if($formError == 1){
				$personalData .= "<p id=\"errore_form\" class='alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}
			$form = '<form action="php/modifica_dati_personali.php?update=<update />&usr=<username />" method="post">';
			$personalData .= str_replace("<update />", $update, $form . file_get_contents("html/dati_personali_update.html"));
			
			$personalData = str_replace("<today_min16anni />", "", $personalData);
			$personalData = str_replace("<today_max110anni />", "", $personalData);
			$annulla = '<a href="visualizza-utente.php?' . ($update == 0 ? 'update=2&' : '') . 'usr=<username />#dati_personali_widget">Annulla</a>';
			$personalData = str_replace("<annulla />", $annulla, $personalData);
		}

		$paginaHTML = str_replace("<dati_personali />", $personalData, $paginaHTML);
		
		$paginaHTML = str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
		$paginaHTML = str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
		$paginaHTML = str_replace("<email />", $datiPersonali["email"], $paginaHTML);
		$paginaHTML = str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
		$paginaHTML = str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);
		$paginaHTML = str_replace("<badge />", $datiPersonali["badge"], $paginaHTML);
		
		//Dettagli abbonamento
		if (!$updateSubscription) {
			$button = '<a href="visualizza-utente.php?usr=<username />&update=<update />#dettagli_abbonamento">Modifica</a>';
			$dettagliAbbonamento = str_replace(
				"<update />",
				($update > 0 ? 0 : 2),
				file_get_contents("html/dettagli_abbonamento.html") . $button
			);
		} else {
			if($formError == 2){
				$dettagliAbbonamento = "<p id='errore_form' class='alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}

			$abbonamentoCorrente = $datiPersonali["nome_abbonamento"];
			$abbonamentiOptions = "<option value=''" . ($abbonamentoCorrente == null ? " selected='selected'" : "") . ">Nessuno</option>";
			foreach ($abbonamenti as $abbonamento) {
				$abbonamento = $abbonamento["nome"];
				$abbonamentiOptions .= "<option value='" . $abbonamento . ($abbonamentoCorrente == $abbonamento ? "' selected='selected'" : "'") . ">" . $abbonamento . "</option>";
			}

			$form = '<form action="php/modifica-abbonamento.php?update=<update />&usr=<username /> " method="post">';
			$dettagliAbbonamento .= str_replace("<update />", $update, $form . file_get_contents("html/dettagli_abbonamento_update.html"));

			$annulla = '<a href="visualizza-utente.php?' . ($update == 0 ? 'update=1&' : '') . 'usr=<username />#dettagli_abbonamento">Annulla</a>';
			$dettagliAbbonamento = str_replace("<annulla />", $annulla, $dettagliAbbonamento);

			$dettagliAbbonamento = str_replace("<abbonamenti />", $abbonamentiOptions, $dettagliAbbonamento);
		}
		$paginaHTML = str_replace("<dettagli_abbonamento />", $dettagliAbbonamento, $paginaHTML);
		$paginaHTML = str_replace("<username />", $user, $paginaHTML);
		
		//Riempimento dati abbonamento
		if($datiPersonali["nome_abbonamento"] == null){
			$paginaHTML = str_replace("<abbonamento />", "Nessuno", $paginaHTML);
			$paginaHTML = str_replace("<scadenza_abbonamento />", "Nessuna", $paginaHTML);
		}
		else{
			$paginaHTML = str_replace("<abbonamento />", $datiPersonali["nome_abbonamento"], $paginaHTML);
			$paginaHTML = str_replace("<scadenza_abbonamento />", $datiPersonali["data_fine"], $paginaHTML);
		}
		$paginaHTML= str_replace("<entrate />", $datiPersonali["entrate"], $paginaHTML);
		$paginaHTML= str_replace("<avviso_acquisto />", "", $paginaHTML);
		
		unset($datiPersonali);

		$paginaHTML = replaceUltimoIngresso($ultimoIngresso, $paginaHTML);
		unset($ultimoIngresso);

		$paginaHTML = replaceSchedeAllenamento($schedeSeguite, $schedeCreate, $admin, $paginaHTML);
		unset($schedeSeguite, $schedeCreate);
	} else {
		$paginaHTML = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>