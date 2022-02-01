<?php
	function initPage($isAdmin) {
		$paginaHTML = file_get_contents("html/area-personale.html");

		if ($isAdmin) {
			$breadcrumb = "Area personale [admin]";
			$admin = "[admin]";
			$widget = "widget_area_personale_admin";
		} else {
			$breadcrumb = "Area personale";
			$admin = "";
			$paginaHTML = str_replace("<gestione_utenti />", "", $paginaHTML);
			$widget = "widget_area_personale";
		}

		$paginaHTML = str_replace("<logout />", "<a href='php/logout.php' id=\"logOut\" lang=\"en\">Logout</a>", $paginaHTML);
		$paginaHTML = str_replace("<breadcrumb />", $breadcrumb, $paginaHTML);
		$paginaHTML = str_replace("<admin />", $admin, $paginaHTML);
		$paginaHTML = str_replace("<widget />", $widget, $paginaHTML);
		$paginaHTML = str_replace("<link_menu />", '<span id="currentLink" title="Area personale">Area personale</span>', $paginaHTML);

		return $paginaHTML;
	}

	function replaceGestioneUtenti($utenti, $userRemoved, $updatePersonalData, $paginaHTML, $nomeRicerca="") {
		$gestioneUtenti = '
			<div id="gestione_utenti" class="widget">
				<h2>Gestione utenti</h2>
				
				<form id="ricerca-utente" action="area-personale.php#gestione_utenti" role="search">
					<label for="nome-ricerca">Cerca utenti</label>
					<input type="text" placeholder="Cerca..." id="nome-ricerca" name="nome-ricerca" value="' . $nomeRicerca . '">
					<button>Cerca</button>
					<a href="area-personale.php#gestione_utenti">Pulisci filtro</a>
				</form>

				<lista_utenti />
			</div>
		';

		$listaUtenti = isset($_GET["nome-ricerca"]) ? "<p>Non è stato trovato nessun utente</p>" : "<p>Nessun utente presente</p>";
		
		if ($utenti != null && count($utenti) > 0) {
			$listaUtenti = "<ul id='lista_utenti'>";
			$utente = array_pop($utenti)["username"];

			$form =
				"	<form action='area-personale.php" . ($updatePersonalData ? "?update=1" : "") . "#gestione_utenti' method='post'>
						<a href='visualizza-utente.php?usr=<username />'><username /></a>
						<input type='hidden' id='user<number />' name='user<number />' value='<username />' />
						<button name='elimina'>Elimina</button>
					</form>
				</li>";

			$listaUtentiEnd = str_replace("<username />", $utente, "<li class='utente' id='last_user'>" . $form);
			$listaUtentiEnd = str_replace("<number />", "_last", $listaUtentiEnd);

			for ($i = 0; $i < count($utenti); ++$i) {
				$utente = $utenti[$i]["username"];
				$listaUtenti .= str_replace("<username />", $utente, "<li class='utente'>" . $form);
				$listaUtenti = str_replace("<number />", $i, $listaUtenti);
			}
			$listaUtenti .= $listaUtentiEnd . "</ul>";
		}

		if ($userRemoved) {
			$listaUtenti = "<p class='notification'>Utente rimosso!</p>" . $listaUtenti;
		}
		$gestioneUtenti = str_replace("<lista_utenti />", $listaUtenti, $gestioneUtenti);
		
		return str_replace("<gestione_utenti />", $gestioneUtenti, $paginaHTML);
	}

	function replaceDatiPersonali($datiPersonali, $updatePersonalData, $formError, $admin, $paginaHTML) {
		$update = 1;
		if(!$updatePersonalData){
			$button = '<a href="area-personale.php?update=<update />#dati_personali_widget" id="buttonModifica">Modifica</a>';
			$personalData = str_replace("<update />", $update, file_get_contents("html/dati_personali.html") . $button);
		}
		else{
			$personalData = "";
			
			if($formError){
				$personalData .= "<p id='errore_form' class='alert'>Si è verificato un errore nella procedura, oppure i dati inseriti non sono validi.</p>";
			}
			$formAction = "php/modifica_dati_personali.php?update=<update />";
			$personalData .= str_replace("<form_action />", $formAction, file_get_contents("html/dati_personali_update.html"));
			$personalData = str_replace("<update />", $update, $personalData);
			
			if ($admin) {
				$personalData = str_replace("<today_min16anni />", "", $personalData);
				$personalData = str_replace("<today_max110anni />", "", $personalData);
			} else {
				$personalData = str_replace("<today_min16anni />", "max='" . date('Y-m-d', strtotime('-16 years')) . "'", $personalData);
				$personalData = str_replace("<today_max110anni />", "min='" . date('Y-m-d', strtotime('-110 years')) . "'", $personalData);
			}

			$annulla = '<a href="area-personale.php#dati_personali_widget">Annulla</a>';
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
				$output= $output . "<a class='scheda_allenamento' href='dettagli-allenamento.php?id=<allenamento />&url=area-personale.php&nomeBreadcrumb=" . urlencode("Area personale" . ($admin? " [admin]" : "")) . "'>";
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
?>