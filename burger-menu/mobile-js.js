function openNav() {
	var topbar= document.getElementById("topbar");
	var burger_menu= document.getElementById("burger-menu");

	topbar.style.width= "100%";
	burger_menu.style.display= "none";

	window.addEventListener("resize", function(){	
		if(window.innerWidth > 600){
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