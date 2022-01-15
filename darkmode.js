function menuClickEvent(){
	if(sessionStorage.getItem('menuDisplay') == 'yes'){
		window.sessionStorage.setItem('menuDisplay', 'no');
		document.getElementById("menu").style.display = "none";
	}
	else {
		window.sessionStorage.setItem('menuDisplay', 'yes');
		document.getElementById("menu").style.display = "block";
	}
}

function modificaDatiPersonaliClickEvent(e){
	window.location.href = "area-personale.php?update=1";
}

function switchTheme(e) {
	if (e.target.checked) {
		document.documentElement.setAttribute('data-theme', 'dark');
		localStorage.setItem('theme', 'dark');
	}
	else {
		document.documentElement.setAttribute('data-theme', 'light');
		localStorage.setItem('theme', 'light');
	}
}

function initDarkMode(){
	toggleSwitch = document.querySelector('#darkmode-switch input[type="checkbox"]');
	toggleSwitch.addEventListener('change', switchTheme);

	if(localStorage.getItem('theme') == null){
		window.localStorage.setItem('theme', 'light');
	}

	if(localStorage.getItem('theme')=='light'){
		document.documentElement.setAttribute('data-theme', 'light');	
	}
	else{
		document.documentElement.setAttribute('data-theme', 'dark');
		toggleSwitch.checked = true;
	}
}

function showAddEsercizioForm (){
	document.getElementById("eliminaEsercizioForm").style.display = "none";
	document.getElementById("aggiungiEsercizioForm").style.display = "block";
}

function showDeleteEsercizioForm (){
	document.getElementById("aggiungiEsercizioForm").style.display = "none";
	document.getElementById("eliminaEsercizioForm").style.display = "block";
}


window.onload= function(){
	initDarkMode();

	//per il menu a comparsa
	window.sessionStorage.setItem('menuDisplay', 'no');
}