function check_validity_nome(e){
	var spanErrore= document.getElementById("errore_nome");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "Il nome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).";
	}
	else{
		document.getElementById("nome").setCustomValidity("");
		spanErrore.innerHTML= "";
	}
}

function check_validity_username(e){
	var spanErrore= document.getElementById("errore_username");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "Lo username inserito non è valido. Deve contenere solo lettere e numeri, senza caratteri speciali (? , * ; + .).";
	}
	else{
		document.getElementById("username").setCustomValidity("");
		spanErrore.innerHTML= "";
	}
}

function check_validity_cognome(e){
	var spanErrore= document.getElementById("errore_cognome");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "Il cognome inserito non è valido. Deve contenere solo lettere, senza caratteri speciali (? , * ; + .).";
	}
	else {
		spanErrore.innerHTML="";
	}
}

function check_validity_email(e){
	var spanErrore= document.getElementById("errore_email");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "La mail inserita non è valida. Deve essere nella forma \"user@gmail.com\".";
	}
	else {
		spanErrore.innerHTML="";
	}
}

function check_validity_password(e){
	var spanErrore= document.getElementById("errore_password");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "La password deve contenere almeno 4 caratteri.";
	}
	else {
		spanErrore.innerHTML="";
	}
}

function check_validity_conferma_password(){
	var spanErrore= document.getElementById("errore_conferma_password");
	var password = document.getElementById("password").value;
	var confermaPassword = document.getElementById("confermaPassword").value;

	if(password != confermaPassword){
		spanErrore.innerHTML= "La password non corrisponde con quella inserita precedentemente.";
	}
	else {
		spanErrore.innerHTML="";
	}
}

function check_validity_telefono(e){
	var spanErrore= document.getElementById("errore_telefono");

	if(!e.target.checkValidity()){
		spanErrore.innerHTML= "Il numero di telefono non è in un formato corretto. Inserisci un numero di cellulare (es. +39 3349854012).";
	}
	else {
		spanErrore.innerHTML="";
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
		else if(data_input < new Date().setFullYear(new Date().getFullYear() - 110)){
			spanErrore.innerHTML+= "Non pensiamo che tu abbia più di 110 anni! Nel caso ci sbaglissimo, complimenti! Scrivi subito ad un admin!";
		}
	}
	else {
		spanErrore.innerHTML="";
	}
}