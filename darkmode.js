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

window.onload= function(){
	toggleSwitch = document.querySelector('#darkmode-switch input[type="checkbox"]');
	toggleSwitch.addEventListener('change', switchTheme);

	if(localStorage.getItem('theme') == null){
		window.localStorage.setItem('theme', 'light');
	}

	if(localStorage.getItem('theme')=='light'){
		document.documentElement.setAttribute('data-theme', 'light');	
	}
	else{
		console.log('qui');
		document.documentElement.setAttribute('data-theme', 'dark');
		toggleSwitch.checked = true;
	}
}		