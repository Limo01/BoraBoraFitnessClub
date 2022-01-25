<?php
	require_once "php/db.php";
	use DB\DBAccess;
	
	function initPage($isAdmin) {
		if ($isAdmin) {
			$breadcrumb = "Area personale [admin]";
			$admin = "[admin]";
			$widget = "widget_area_personale_admin";
			$personalData = str_replace("<today_min16anni />", "", $personalData);
			$personalData = str_replace("<today_max110anni />", "", $personalData);
		} else {
			$breadcrumb = "Area personale";
			$admin = "";
			$paginaHTML = str_replace("<gestione_utenti />", "", $paginaHTML);
			$widget = "widget_area_personale";
		}

		$paginaHTML = file_get_contents("html/area-personale.html");
		$paginaHTML = str_replace("<logout />", "<a href='php/logout.php'>Logout</a>", $paginaHTML);
		$paginaHTML = str_replace("<breadcrumb />", $breadcrumb, $paginaHTML);
		$paginaHTML = str_replace("<admin />", $admin, $paginaHTML);
		$paginaHTML = str_replace("<widget />", $widget, $paginaHTML);

		return $paginaHTML;
	}

	function replaceGestioneUtenti($utenti, $userRemoved, $paginaHTML) {
		$gestioneUtenti = '
			<div id="gestione_utenti" class="widget">
				<h2>Gestione utenti</h2>

				<lista_utenti />
			</div>
		';

		$listaUtenti = "<p>Nessun utente presente</p>";
		
		if ($utenti != null && count($utenti) > 0) {
			$listaUtenti = "<ul id='lista_utenti'>";
			$utente = array_pop($utenti)["username"];
			$listaUtentiEnd =
				"<li class='utente' id='last_user'>
					<a href='visualizza-utente.php?usr=" . $utente ."'>" . $utente . "</a>
					<form action='area-personale.php" . ($updatePersonalData ? "?update=1" : "") . "#gestione_utenti' method='post'>
						<input type='hidden' name='user' value='" . $utente . "' />
						<button name='elimina'>Elimina</button>
					</form>
				</li>";
			if (count($utenti) > 0) {
				foreach ($utenti as $utente) {
					$utente = $utente["username"];
					$listaUtenti .=
					"<li class='utente'>
					<a href='visualizza-utente.php?usr=" . $utente ."'>" . $utente . "</a>
					<form action='area-personale.php" . ($updatePersonalData ? "?update=1" : "") . "#gestione_utenti' method='post'>
					<input type='hidden' name='user' value='" . $utente . "' />
					<button name='elimina'>Elimina</button>
					</form>
					</li>";
				}
			}
			$listaUtenti .= $listaUtentiEnd . "</ul>";
		}

		if ($userRemoved) {
			$listaUtenti = "<p class='alert'>Utente rimosso!</p>" . $listaUtenti;
		}
		$gestioneUtenti = str_replace("<lista_utenti />", $listaUtenti, $gestioneUtenti);
		
		return str_replace("<gestione_utenti />", $gestioneUtenti, $paginaHTML);
	}

	function replaceDatiPersonali($datiPersonali, $updatePersonalData, $paginaHTML) {
		$update = 1;
		if(!$updatePersonalData){
			$button = '<a href="area-personale.php?update=<update />">Modifica</a>';
			$personalData = str_replace("<update />", $update, file_get_contents("html/dati_personali.html") . $button);
		}
		else{
			$personalData = "";
			
			if($formError){
				$personalData .= "<p id='errore_form' class'alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}
			$form = '<form action="php/modifica_dati_personali.php?update=<update />" method="post">';
			$personalData .= str_replace("<update />", $update, $form . file_get_contents("html/dati_personali_update.html"));
			
			$personalData = str_replace("<today_min16anni />", date('Y-m-d', strtotime('-16 years')), $personalData);
			$personalData = str_replace("<today_max110anni />", date('Y-m-d', strtotime('-110 years')), $personalData);

			$annulla = '<a href="area-personale.php">Annulla</a>';
			$personalData = str_replace("<annulla />", $annulla, $personalData);
		}

		$paginaHTML = str_replace("<dati_personali />", $personalData, $paginaHTML);

		$paginaHTML = str_replace("<username />", $datiPersonali["username"], $paginaHTML);
		$paginaHTML = str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
		$paginaHTML = str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
		$paginaHTML = str_replace("<email />", $datiPersonali["email"], $paginaHTML);
		$paginaHTML = str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
		$paginaHTML = str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);
		return str_replace("<badge />", $datiPersonali["badge"], $paginaHTML);
	}

	function replaceDatiAbbonamento($datiPersonali, $paginaHTML) {
		$paginaHTML = str_replace("<dettagli_abbonamento />", file_get_contents("html/dettagli_abbonamento.html"), $paginaHTML);

		if($datiPersonali["nome_abbonamento"] == null){
			$paginaHTML = str_replace("<abbonamento />", "Nessuno", $paginaHTML);
			$paginaHTML = str_replace("<scadenza_abbonamento />", "Nessuna", $paginaHTML);
		}
		else{
			$paginaHTML = str_replace("<abbonamento />", $datiPersonali["nome_abbonamento"], $paginaHTML);
			$paginaHTML = str_replace("<scadenza_abbonamento />", $datiPersonali["data_fine"], $paginaHTML);
		}
		$paginaHTML= str_replace("<entrate />", $datiPersonali["entrate"], $paginaHTML);

		if(isset($_GET["acquisto"]) && $_GET["acquisto"]==1){
			$paginaHTML= str_replace("<avviso_acquisto />", "<p class='notification'>Hai appena effettutato un acquisto!</p>", $paginaHTML);
		}
		else{
			$paginaHTML= str_replace("<avviso_acquisto />", "", $paginaHTML);
		}

		if($datiPersonali["data_fine"]!=null && $datiPersonali["data_fine"] < date("Y-m-d")){
			$paginaHTML = str_replace("<avviso_abbonamento />", "<p class='alert'>Attenzione! Il tuo abbonamenoto è scaduto.</p>", $paginaHTML);
		} else {
			$paginaHTML = str_replace("<avviso_abbonamento />", "", $paginaHTML);
		}
		return $paginaHTML;
	}

	function replaceUltimoIngresso($ultimoIngresso, $paginaHTML) {
		if($ultimoIngresso == null){
			$paginaHTML = str_replace("<data_ingresso />", "Nessuna", $paginaHTML);
			$paginaHTML = str_replace("<ora_ingresso />", "Nessuna", $paginaHTML);
		}
		else{
			$ultimoIngresso = explode(" ", $ultimoIngresso["dataora_entrata"]);

			$paginaHTML = str_replace("<data_ingresso />", $ultimoIngresso[0], $paginaHTML);
			$paginaHTML = str_replace("<ora_ingresso />", $ultimoIngresso[1], $paginaHTML);
		}
		return $paginaHTML;
	}
	
	function createDisplayAllenamenti($schede, $admin) {
		$output= "<div class='display_allenamenti'>";
			foreach($schede as $allenamento) {
				$output= $output . "<a class='scheda_allenamento' href='dettagli-allenamento.php?id=<allenamento />&url=area-personale.php&nomeBreadcrumb=Area_personale" . ($admin? " [admin]" : "") . "'>";
				$output= $output . "<h3>" . $allenamento["nome"] . "</h3>";
				$output= $output . "<p>" . $allenamento["descrizione"] . "</p></a>";
				$output = str_replace("<allenamento />", $allenamento["id"], $output);
			}
			return $output . "</div>";
	}

	function replaceSchedeAllenamento($schedeSeguite, $schedeCreate, $admin, $paginaHTML) {
		//Riempimento dati schede seguite
		if($schedeSeguite == null){
			$paginaHTML = str_replace("<allenamenti_seguiti />", "<p>Nessuna scheda allenamento seguita</p>", $paginaHTML);
		}
		else{
			$output = createDisplayAllenamenti($schedeSeguite, $admin);
			$paginaHTML = str_replace("<allenamenti_seguiti />", $output, $paginaHTML);
		}

		//Riempimento dati schede create
		if($schedeCreate == null){
			$paginaHTML = str_replace("<allenamenti_creati />", "<p>Nessun allenamento creato</p>", $paginaHTML);
		}
		else{
			$output = createDisplayAllenamenti($schedeCreate, $admin);
			$paginaHTML = str_replace("<allenamenti_creati />", $output, $paginaHTML);
		}

		return $paginaHTML;
	}

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