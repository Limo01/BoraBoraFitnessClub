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
	}
	else {
		spanErrore.innerHTML="";
	}
}