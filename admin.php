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

	$paginaHTML = file_get_contents("html/admin.html");

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	
	$updatePersonalData = false;
	if(isset($_GET["update"]) and $_GET["update"]==1){
		$updatePersonalData= true;
	}

	$formError = false;
	if(isset($_GET["form_error"]) and $_GET["form_error"]==1){
		$formError = true;
	}

	if ($connessioneOK) {
		$result = $connessione->doReadQuery("SELECT * FROM utente WHERE username=? and is_admin=true", "s", $user);
		if ($result != null) {
			$datiPersonali = $result[0];

			$utenti = $connessione->doReadQuery("SELECT username FROM utente WHERE username!=?", "s", $user);

			$ultimoIngresso = $connessione->doReadQuery("SELECT dataora_entrata FROM accesso WHERE username_utente=? order by dataora_entrata DESC limit 1", "s", $user);

			if($ultimoIngresso!=null){
				$ultimoIngresso = $ultimoIngresso[0];
			}

			$schedeSeguite =  $connessione->doReadQuery("SELECT id, nome, descrizione FROM utente_allenamento JOIN allenamento ON (utente_allenamento.id_allenamento=allenamento.id) WHERE utente_allenamento.username_utente=? AND utente_allenamento.username_utente!=allenamento.username_utente", "s", $user);

			$schedeCreate = $connessione->doReadQuery("SELECT id, nome, descrizione FROM allenamento WHERE allenamento.username_utente=?", "s", $user);

			$connessione->closeConnection();

			//Gestione utenti
			$listaUtenti = "<p>Nessun utente presente</p>";
			
			if ($utenti != null) {
				$listaUtenti = '<ul id="lista_utenti">';
				foreach ($utenti as $utente) {
					$listaUtenti .= '<li class="utente">' . $utente["username"] . "</li>";
				}
				$listaUtenti .= "</ul>";
			}
			$paginaHTML = str_replace("<lista_utenti />", $listaUtenti, $paginaHTML);

			//Informazioni personali
			if(!$updatePersonalData){
				$personalData= 
				"<dl class=\"dl_inline\">
					<dt>Nome</dt>
					<dd>
						<nome />
					</dd>

					<dt>Cognome</dt>
					<dd>
						<cognome />
					</dd>

					<dt>Email</dt>
					<dd>
						<email />
					</dd>

					<dt>Telefono</dt>
					<dd>
						<numero_telefono />
					</dd>

					<dt>Data di nascita</dt>
					<dd>
						<data_nascita />
					</dd>

					<dt>Badge ID</dt>
					<dd>
						<badge />
					</dd>
				</dl>
				<a href=\"admin.php?update=1\">
					<button id=\"buttonModDati\">Modifica</button>
				</a>";
			}
			else{
				$personalData= "";
				
				if($formError){
					$personalData = $personalData . "<p id=\"errore_form\">Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
				}

				$personalData= $personalData . 
					"<form action=\"php/modifica_dati_personali.php\" method=\"post\">
						<label for=\"nome\">Nome</label>
						<input type=\"text\" id=\"nome\" name=\"nome\" value=\"<nome />\" required pattern=\"^[a-zA-Z-' àèìòùáéíóú]*$\" onblur=\"check_validity_nome(event)\" >
						<p id=\"errore_nome\"class=\"errore_form\"></p>
						
						<label for=\"cognome\">Cognome</label>
						<input type=\"text\" id=\"cognome\" name=\"cognome\" value=\"<cognome />\" required pattern=\"^[a-zA-Z-' àèìòùáéíóú]*$\" onblur=\"check_validity_cognome(event)\">
						<p id=\"errore_cognome\"class=\"errore_form\"></p>
						
						<label for=\"email\">Email</label>
						<input type=\"email\" id=\"email\" name=\"email\" value=\"<email />\" required onblur=\"check_validity_email(event)\">
						<p id=\"errore_email\"class=\"errore_form\"></p>

						<label for=\"telefono\">Numero di telefono</label>
						<input type=\"text\" id=\"telefono\" name=\"telefono\" value=\"<numero_telefono />\" required pattern=\"^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$\" onblur=\"check_validity_telefono(event)\">
						<p id=\"errore_telefono\"class=\"errore_form\"></p>

						<label for=\"data_nascita\">Data di nascita</label>
						<input type=\"date\" id=\"data_nascita\" name=\"data_nascita\" value=\"<data_nascita />\" required onblur=\"check_validity_data_nascita(event)\">
						<p id=\"errore_data_nascita\"class=\"errore_form\"></p>
						
						<button>Conferma modifica</button>
					</form>";
			}

			$paginaHTML = str_replace("<dati_personali />", $personalData, $paginaHTML);

			$paginaHTML = str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
			$paginaHTML = str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
			$paginaHTML = str_replace("<email />", $datiPersonali["email"], $paginaHTML);
			$paginaHTML = str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
			$paginaHTML = str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);
			$paginaHTML = str_replace("<badge />", $datiPersonali["badge"], $paginaHTML);		

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
		}
		else {
			$connessione->closeConnection();
			header("Location: area-personale.php");
			$paginaHTML = "<p>Accesso negato</p>";	//Viene visualizzato solo se l'utente ha disabilitato redirect
		}
	}
	else {
		$paginaHTML = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>