<?php
	require_once "php/db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("html/admin.html");

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$user = "admin";
	
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
		$datiPersonali = $result[0];

		$connessione->closeConnection();

		//Informazioni personali
		if(!$updatePersonalData){
			$personalData =
			"<dl>
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

				<dt>Numero di telefono</dt>
				<dd>
					<numero_telefono />
				</dd>

				<dt>Data di nascita</dt>
				<dd>
					<data_nascita />
				</dd>
			</dl>
			<button id=\"buttonModDati\" onclick=\"modificaDatiPersonaliClickEvent()\">Modifica</button>";
		}
		else{
			$personalData= "";
			
			if($formError){
				$personalData = $personalData . "<p id=\"errore_form\">Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}

			$personalData= $personalData . 
				"<form action=\"/modifica_dati_personali.php\" method=\"post\">
					<label for=\"nome\">Nome:</label><br>
					<input type=\"text\" id=\"nome\" name=\"nome\" value=\"<nome />\" required pattern=\"^[a-zA-Z-' àèìòùáéíóú]*$\" onblur=\"check_validity_nome(event)\" >
					<span id=\"errore_nome\"class=\"errore_form\"></span>
					<br><br>
					
					<label for=\"cognome\">Cognome:</label><br>
					<input type=\"text\" id=\"cognome\" name=\"cognome\" value=\"<cognome />\" required pattern=\"^[a-zA-Z-' àèìòùáéíóú]*$\" onblur=\"check_validity_cognome(event)\">
					<span id=\"errore_cognome\"class=\"errore_form\"></span>
					<br><br>
					
					<label for=\"email\">Email:</label><br>
					<input type=\"email\" id=\"email\" name=\"email\" value=\"<email />\" required onblur=\"check_validity_email(event)\">
					<span id=\"errore_email\"class=\"errore_form\"></span>
					<br><br>

					<label for=\"telefono\">Numero di telefono:</label><br>
					<input type=\"text\" id=\"telefono\" name=\"telefono\" value=\"<numero_telefono />\" required pattern=\"^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$\" onblur=\"check_validity_telefono(event)\">
					<span id=\"errore_telefono\"class=\"errore_form\"></span>
					<br><br>

					<label for=\"data_nascita\">Data di nascita:</label><br>
					<input type=\"date\" id=\"data_nascita\" name=\"data_nascita\" value=\"<data_nascita />\" required onblur=\"check_validity_data_nascita(event)\">
					<span id=\"errore_data_nascita\"class=\"errore_form\"></span>
					<br><br>
					
					<input type=\"submit\" value=\"Conferma modifica\">
				</form>";
		}

		$paginaHTML = str_replace("<dati_personali />", $personalData, $paginaHTML);

		$paginaHTML = str_replace("<nome />", $datiPersonali["nome"], $paginaHTML);
		$paginaHTML = str_replace("<cognome />", $datiPersonali["cognome"], $paginaHTML);
		$paginaHTML = str_replace("<email />", $datiPersonali["email"], $paginaHTML);
		$paginaHTML = str_replace("<numero_telefono />", $datiPersonali["numero_telefono"], $paginaHTML);
		$paginaHTML = str_replace("<data_nascita />", $datiPersonali["data_nascita"], $paginaHTML);

		//Lista utenti
		
	} else {
		$listaClienti = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	echo $paginaHTML;
?>