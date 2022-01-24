<?php
	require_once "php/db.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$user = $_SESSION["username"];
	}
	else{
		header("location: autenticazione.php");
		return;
	}
	if ($_SESSION["isAdmin"] == true) {
		$params = "";
		if (count($_GET) != 0) {
			$params = "?";
			foreach ($_GET as $name => $value) {
				$params .= $name . "=" . $value . "&";
			}
			substr_replace($params ,"", -1); //Elimina l'ultimo '&'
		}

		header("Location: admin.php" . $params);
		die("Errore: il redirect è stato disabilitato");
	}

	$paginaHTML = file_get_contents("html/area-personale.html");
	$paginaHTML = str_replace("<logout />", "<a href='php/logout.php'>Logout</a>", $paginaHTML);
	$paginaHTML = str_replace("<breadcrumb />", "Area personale", $paginaHTML);
	$paginaHTML = str_replace("<admin />", "", $paginaHTML);
	$paginaHTML = str_replace("<gestione_utenti />", "", $paginaHTML);
	$paginaHTML = str_replace("<widget />", "widget_area_personale", $paginaHTML);

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	
	$updatePersonalData = false;
	if(isset($_GET["update"]) && $_GET["update"] === "1"){
		$updatePersonalData= true;
	}

	$formError = false;
	if(isset($_GET["form_error"]) && $_GET["form_error"] === "1"){
		$formError = true;
	}

	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM utente WHERE username=?", "s", $user);
		$datiPersonali = $result[0];

		$ultimoIngresso = $connessione->doReadQuery("SELECT dataora_entrata FROM accesso WHERE username_utente=? order by dataora_entrata DESC limit 1", "s", $user);

		if($ultimoIngresso!=null){
			$ultimoIngresso = $ultimoIngresso[0];
 		}

		$schedeSeguite =  $connessione->doReadQuery("SELECT id, nome, descrizione FROM utente_allenamento JOIN allenamento ON (utente_allenamento.id_allenamento=allenamento.id) WHERE utente_allenamento.username_utente=? AND utente_allenamento.username_utente!=allenamento.username_utente", "s", $user);

		$schedeCreate = $connessione->doReadQuery("SELECT id, nome, descrizione FROM allenamento WHERE allenamento.username_utente=?", "s", $user);

		$connessione->closeConnection();

		//Informazioni personali
		$update = 1;
		if(!$updatePersonalData){
			$button = '<a href="area-personale.php?update=<update />">Modifica</a>';
			$personalData = str_replace("<update />", $update, file_get_contents("html/dati_personali.html") . $button);
		}
		else{
			$personalData = "";
			
			if($formError){
				$personalData .= "<p id=\"errore_form\" class'alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}
			$form = '<form action="php/modifica_dati_personali.php?update=<update />" method="post">';
			$personalData .= str_replace("<update />", $update, $form . file_get_contents("html/dati_personali_update.html"));
			
			$personalData = str_replace("<today_min16anni />", date('Y-m-d', strtotime('-16 years')), $personalData);
			$personalData = str_replace("<today_max110anni />", date('Y-m-d', strtotime('-110 years')), $personalData);

			$annulla = '<a href="area-personale.php">Annulla</a>';
			$personalData = str_replace("<annulla />", $annulla, $personalData);
		}

		$paginaHTML = str_replace("<dati_personali />", $personalData, $paginaHTML);

		$paginaHTML = str_replace("<username />", $user, $paginaHTML);
		$paginaHTML = str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
		$paginaHTML = str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
		$paginaHTML = str_replace("<email />", $datiPersonali["email"], $paginaHTML);
		$paginaHTML = str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
		$paginaHTML = str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);
		$paginaHTML = str_replace("<badge />", $datiPersonali["badge"], $paginaHTML);		

		//Riempimento dati abbonamento
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
			$paginaHTML= str_replace("<avviso_abbonamento />", "<p class='alert'>Attenzione! Il tuo abbonamenoto è scaduto.</p>", $paginaHTML);
		}
		else{
			$paginaHTML= str_replace("<avviso_abbonamento />", "", $paginaHTML);
		}

		//Riempimento dati ultimo ingresso
		if($ultimoIngresso == null){
			$paginaHTML = str_replace("<data_ingresso />", "Nessuna", $paginaHTML);
			$paginaHTML = str_replace("<ora_ingresso />", "Nessuna", $paginaHTML);
		}
		else{
			$ultimoIngresso = explode(" ", $ultimoIngresso["dataora_entrata"]);

			$paginaHTML = str_replace("<data_ingresso />", $ultimoIngresso[0], $paginaHTML);
			$paginaHTML = str_replace("<ora_ingresso />", $ultimoIngresso[1], $paginaHTML);
		}

		//Riempimento dati schede seguite
		if($schedeSeguite == null){
			$paginaHTML = str_replace("<allenamenti_seguiti />", "<p>Nessuna scheda allenamento seguita</p>", $paginaHTML);
		}
		else{
			$output= "<div class=\"display_allenamenti\">";
			foreach($schedeSeguite as $allenamento){
				$output= $output . "<a class='article_allenamento' href=\"dettagli-allenamento.php?id=" . $allenamento["id"] . "&url=area-personale.php&nomeBreadcrumb=Area%personale\">";
				$output= $output . "<h3>" . $allenamento["nome"] . "</h3>";
				$output= $output . "<p>" . $allenamento["descrizione"] . "</p></a>";
			}
			$output= $output . "</div>";
			$paginaHTML = str_replace("<allenamenti_seguiti />", $output, $paginaHTML);
		}

		//Riempimento dati schede create
		if($schedeCreate == null){
			$paginaHTML = str_replace("<allenamenti_creati />", "<p>Nessun allenamento creato</p>", $paginaHTML);
		}
		else{
			$output= "<div class=\"display_allenamenti\">";
			foreach($schedeCreate as $allenamento){
				$output= $output . "<article class=\"article_allenamento\"><a href=\"dettagli-allenamento.php?id=" . $allenamento["id"] . "&url=area-personale.php&nomeBreadcrumb=Area%personale\">";
				$output= $output . "<h3>" . $allenamento["nome"] . "</h3>";
				$output= $output . "<p>" . $allenamento["descrizione"] . "</p></a></article>";	
			}
			$output= $output . "</div>";
			$paginaHTML = str_replace("<allenamenti_creati />", $output, $paginaHTML);
		}
	} else {
		$paginaHTML = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>