<?php
	require_once "php/db.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$admin = $_SESSION["username"];
	} else{
		header("location: autenticazione.php");
		die("Errore: il redirect è stato disabilitato");
	}

	if (!$_SESSION["isAdmin"]) {
		header("location: area-personale.php");
		die("Accesso negato!");
	}

	if (isset($_GET["usr"])) {
		$user = $_GET["usr"];
	} else {
		die("Errore: nessun utente selezionato");
	}

	if ($admin == $user) {
		header("location: area-personale.php");
		die("Errore: il redirect è stato disabilitato");
	}

	$paginaHTML = file_get_contents("html/modifica-utente.html");
	$paginaHTML = str_replace("<username />", $user, $paginaHTML);
	
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
	
	$formError = false;
	if(isset($_GET["form_error"]) && $_GET["form_error"]==1){
		$formError = true;
	}

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	
	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM utente WHERE username=?", "s", $user);
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
			$personalData = str_replace("<update />", ($update > 0 ? 0 : 1), file_get_contents("html/dati_personali.html"));
			$personalData .= '
			<a href="modifica-utente.php?usr=<username />&update=
			<update />">
				<button id="buttonModDati">Modifica</button>
			</a>';
		}
		else{
			$personalData = "";
			
			if($formError){
				$personalData .= "<p id=\"errore_form\" class'alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}

			$personalData .= str_replace("<update />", $update, file_get_contents("html/dati_personali_update.html"));
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
			$oldUpdate = $update;
			$update = ($update > 0 ? 0 : 2);

			$dettagli_abbonamento = '
				<dl class="dl_inline">
					<dt>Abbonamento attivo</dt>
					<dd>
						<abbonamento />
					</dd>

					<dt>Data inizio abbonamento</dt>
					<dd>
						<inizio_abbonamento />
					</dd>

					<dt>Scadenza abbonamento</dt>
					<dd>
						<scadenza_abbonamento />
					</dd>

					<dt>Entrate singole disponibili</dt>
					<dd>
						<entrate />
					</dd>
				</dl>

				<a href="modifica-utente.php?usr=' . $user . '&update=' . $update . '">Modifica</a>
			';

			$update = $oldUpdate;
		} else {
			$abbonamentoCorrente = $datiPersonali["nome_abbonamento"];
			$abbonamentiOptions = "<option value='Nessuno'" . ($abbonamentoCorrente == null ? " selected='selected'" : "") . ">Nessuno</option>";
			foreach ($abbonamenti as $abbonamento) {
				$abbonamento = $abbonamento["nome"];
				$abbonamentiOptions .= "<option value='" . $abbonamento . ($abbonamentoCorrente == $abbonamento ? "' selected='selected'" : "'") . ">" . $abbonamento . "</option>";
			}

			$dettagli_abbonamento = "
				<form action=\"php/modifica_dati_personali.php?update=" . $update . "&usr=" . $user . "\" method=\"post\">
					<label for=\"abbonamento\">Abbonamento attivo</label>
					
					<select name='abbonamento' id='abbonamento' required>
						" . $abbonamentiOptions . "
					</select>
					
					<label for=\"scadenza\">Scadenza</label>
					<input type=\"date\" name=\"scadenza\" id=\"scadenza\" value=\"<scadenza_abbonamento />\" required >
					<p id=\"errore_scadenza\"class=\"errore_form\"></p>
					
					<label for=\"entrate\">Entrate singole disponibili</label>
					<input type=\"number\" name=\"entrate\" id=\"entrate\" value=\"<entrate />\" required >
					<p id=\"errore_entrate\"class=\"errore_form\"></p>

					<button>Conferma modifica</button>
				</form>
			";
		}
		$paginaHTML = str_replace("<dettagli_abbonamento />", $dettagli_abbonamento, $paginaHTML);
		
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
				$output= $output . "<article class=\"article_allenamento\"><a href=\"dettagli-allenamento.php?id=" . $allenamento["id"] . "\">";
				$output= $output . "<h3>" . $allenamento["nome"] . "</h3>";
				$output= $output . "<p>" . $allenamento["descrizione"] . "</p></a></article>";	
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
				$output= $output . "<article class=\"article_allenamento\"><a href=\"dettagli-allenamento.php?id=" . $allenamento["id"] . "\">";
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