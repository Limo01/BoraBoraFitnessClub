/*Funzioni per darkmode*/
function switchTheme(e) {
	if (e.target.checked) {
		document.documentElement.setAttribute("data-theme", "dark");
		localStorage.setItem("theme", "dark");
		document.getElementById("darkmode-switch").setAttribute("title", "Passa a modalità giorno");
		document.getElementById("darkmode-switch").setAttribute("aria-label", "Passa a modalità giorno");
	} else {
		document.documentElement.setAttribute("data-theme", "light");
		localStorage.setItem("theme", "light");
		document.getElementById("darkmode-switch").setAttribute("title", "Passa a modalità notte");
		document.getElementById("darkmode-switch").setAttribute("aria-label", "Passa a modalità notte");
	}
}

function initDarkMode() {
	var toggleSwitch= document.getElementById("checkbox");
	toggleSwitch.addEventListener("change", switchTheme);

	if (localStorage.getItem("theme") == null) {
		window.localStorage.setItem("theme", "light");
	}

	if (localStorage.getItem("theme") == "light") {
		document.documentElement.setAttribute("data-theme", "light");
	} else {
		document.documentElement.setAttribute("data-theme", "dark");
		toggleSwitch.checked = true;
	}
}

/*Funzioni di validazione dei campi*/
function check_validity_nome(e){
	var pErrore= document.getElementById("errore_nome");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "Il nome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).";
	}
	else{
		document.getElementById("nome").setCustomValidity("");
		pErrore.innerHTML= "";
	}
}

function check_validity_username(e){
	var pErrore= document.getElementById("errore_username");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "Lo username inserito non è valido. Deve contenere solo lettere e numeri, senza caratteri speciali (? , * ; + .).";
	}
	else{
		document.getElementById("username").setCustomValidity("");
		pErrore.innerHTML= "";
	}
}

function check_validity_cognome(e){
	var pErrore= document.getElementById("errore_cognome");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "Il cognome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).";
	}
	else {
		pErrore.innerHTML="";
	}
}

function check_validity_email(e){
	var pErrore= document.getElementById("errore_email");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "La mail inserita non è valida. Deve essere nella forma \"user@gmail.com\".";
	}
	else {
		pErrore.innerHTML="";
	}
}

function check_validity_password(e){
	var pErrore= document.getElementById("errore_password");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "La password deve contenere almeno 4 caratteri.";
	}
	else {
		pErrore.innerHTML="";
	}
}

function check_validity_conferma_password(){
	var pErrore= document.getElementById("errore_conferma_password");
	var password = document.getElementById("password").value;
	var confermaPassword = document.getElementById("confermaPassword").value;

	if(password != confermaPassword){
		pErrore.innerHTML= "La password non corrisponde con quella inserita precedentemente.";
	}
	else {
		pErrore.innerHTML="";
	}
}

function check_validity_telefono(e){
	var pErrore= document.getElementById("errore_telefono");

	if(!e.target.checkValidity()){
		pErrore.innerHTML= "Il numero di telefono non è in un formato corretto. Inserisci un numero di cellulare (es. +39 3349854012).";
	}
	else {
		pErrore.innerHTML="";
	}
}

function check_validity_data_nascita(e){
	var spanErrore= document.getElementById("errore_data_nascita");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "La data di nascita non è in un formato corretto.";
		var data_input= new Date(e.target.value);

		if(data_input > new Date().setFullYear(new Date().getFullYear() - 16)){
			if(data_input > new Date()){
				spanErrore.innerHTML+= " Non pensiamo proprio che tu venga dal futuro!";
			}
			else{
				spanErrore.innerHTML = "Ti ricordiamo che devi avere minimo 16 anni!";
			}
		}
		else if(data_input < new Date().setFullYear(new Date().getFullYear() - 120)){
			spanErrore.innerHTML+= " Secondo <span lang='en'>Wikipedia</span> non esistono persone con più di 120 anni!";
		}
	}
	else {
		spanErrore.innerHTML="";
	}
}

function disableScadenza() {
	abbonamento = document.getElementById("abbonamento");
	var errore = document.getElementById("errore_scadenza");
	var abbonamentoSel = abbonamento.options[abbonamento.selectedIndex].value;
	var scadenza = document.getElementById("scadenza");
	if (abbonamentoSel == "") {
		if (!scadenza.getAttribute("disabled")) {
			scadenza.setAttribute("disabled", "disabled");
			errore.innerHTML = "";
		}
	}
	else {
		if (!scadenza.getAttribute("required"))
			scadenza.setAttribute("required", "required");

		if (scadenza.getAttribute("disabled") == "disabled")
			scadenza.removeAttribute("disabled");
	}
}

function check_validity_scadenza(e) {
	disableScadenza();
	var errore = document.getElementById("errore_scadenza");

	if (!e.target.checkValidity()) {
		errore.innerHTML= "La data inserita non è in un formato corretto.";
	}
	else {
		var data_input= new Date(e.target.value);
		if (data_input <= new Date()) {
			errore.innerHTML = "La data di scadenza deve essere una data futura.";
		}
		else
			errore.innerHTML = "";
	}
}

function check_validity_entrate(e) {
	var errore = document.getElementById("errore_entrate");

	if (!e.target.checkValidity()) {
		var entrate_input = e.target.value;
		
		if (entrate_input < 0)
			errore.innerHTML = "Non è possibile inserire un numero negativo di entrate.";
		else if (entrate_input > 2147483647)
			errore.innerHTML = "Il numero inserito di entrate è superiore al massimo consentito.";
	}
	else
		errore.innerHTML = "";
}

/*Funzione per aggiungere gli eventi blur nella registrazione*/
function addOnBlurEventInput(){
	if(document.getElementById("nome")!=null)
		document.getElementById("nome").addEventListener("blur", check_validity_nome);

	if(document.getElementById("cognome")!=null)
		document.getElementById("cognome").addEventListener("blur", check_validity_cognome);

	if(document.getElementById("dataNascita")!=null)
		document.getElementById("dataNascita").addEventListener("blur", check_validity_data_nascita);

	if(document.getElementById("username")!=null)
		document.getElementById("username").addEventListener("blur", check_validity_username);

	if(document.getElementById("password")!=null)
		document.getElementById("password").addEventListener("blur", check_validity_password);

	if(document.getElementById("confermaPassword")!=null)
		document.getElementById("confermaPassword").addEventListener("blur", check_validity_conferma_password);

	if(document.getElementById("email")!=null)
		document.getElementById("email").addEventListener("blur", check_validity_email);

	if(document.getElementById("telefono")!=null)
		document.getElementById("telefono").addEventListener("blur", check_validity_telefono);
	
	if (document.getElementById("abbonamento") != null)
		document.getElementById("abbonamento").addEventListener("blur", disableScadenza);
	
	if (document.getElementById("scadenza") != null)
		document.getElementById("scadenza").addEventListener("blur", check_validity_scadenza);
	
	if (document.getElementById("entrate") != null)
		document.getElementById("entrate").addEventListener("blur", check_validity_entrate);
}

/*Funzioni per il burger menu*/
function openNav() {
	var topbar= document.getElementById("topbar");
	var burger_menu= document.getElementById("burger-menu");

	topbar.style.width= "100%";
	burger_menu.style.display= "none";

	window.addEventListener("resize", function(){	
		if(window.innerWidth > 768){
			topbar.style.width= "100%";
			burger_menu.style.display= "none";
		}
		else{
			topbar.style.width= "0%";
			burger_menu.style.display= "block";
		}
	});
}

function closeNav() {
	var topbar= document.getElementById("topbar");
	var burger_menu= document.getElementById("burger-menu");

	topbar.style.width = "0%";

	topbar.addEventListener("transitionend", function(){
		if(topbar.style.width == "0%"){
			burger_menu.style.display= "block";
		}
	});
}

function initBurgerMenu(){
	document.getElementById("burger-menu").addEventListener("click", openNav);
	document.getElementById("closebtn").addEventListener("click", closeNav);
}

/*Funzione per il contatore di persone nella home*/
function initCounter(){
	if(document.getElementById("contatorePersone") == null)
		return false;

	setInterval(function () {
		var xhttp = new XMLHttpRequest();
		var n= parseInt(document.getElementById("contatorePersone").innerHTML);

		xhttp.onreadystatechange = function() {
		   	if (this.readyState == 4 && this.status == 200) {
		    	document.getElementById("contatorePersone").innerHTML = this.responseText;
		    }
		};
		
		xhttp.open("GET", "php/number_generator.php?n="+n, true);
		xhttp.send();
	}, 8000);
}

function scrollMenu(){
	if(window.location.hash && window.innerWidth > 768){
		var id = document.getElementById(window.location.hash.substring(1));
		if(id != null){
			var top = id.offsetTop;
    		window.scrollTo(0, top-50); 
		}
	}
}

window.onload = function () {
	initBurgerMenu();
	initDarkMode();
	addOnBlurEventInput();
	initCounter();

	document.getElementById("tornaSu").style.display = "none";

	//per il menu a comparsa
	window.sessionStorage.setItem("menuDisplay", "no");

	//per evitare che i link alle ancore siano in parte coperti dal menu
	scrollMenu();
	
	if (document.getElementById("scadenza") != null)
		disableScadenza();
};

/*Funzione per il torna su*/
window.addEventListener(
	"scroll",
	function () {
		tornaSu = document.getElementById("tornaSu");
		if (
			document.body.scrollTop > 300 ||
			document.documentElement.scrollTop > 300
		) {
			tornaSu.style.display = "block";
		} else {
			tornaSu.style.display = "none";
		}
	}
);

//scroll dopo link interno alla pagina
window.addEventListener(
	"hashchange",
	function () {
		if(window.location.hash && window.innerWidth > 768){
			var id = document.getElementById(window.location.hash.substring(1));
			if(id != null){
				var top = id.offsetTop;
				window.scrollTo(0, top-50); 
			}
		}
	}
);
