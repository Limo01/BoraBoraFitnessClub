<?php
	require_once "db.php";
	use DB\DBAccess;

	session_start();

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
		header("location: ../autenticazione.php");
		return;
	}

	if(isset($_POST["tipo-abbonamento"])){
		$_SESSION["tipo-abbonamento"] = $_POST["tipo-abbonamento"];

		$paginaHTML = file_get_contents("../html/conferma-acquisto.html");
		echo $paginaHTML;

		header ("Refresh: 7.5;URL='acquisto.php'");
	}
	else{
		die("Si è verificato un errore nel server!");
	}
?>