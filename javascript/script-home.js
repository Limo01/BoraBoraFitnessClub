function initCounter(){
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

var handlerPrec= window.onload;

window.onload= function(){
	handlerPrec();
	initCounter();
}