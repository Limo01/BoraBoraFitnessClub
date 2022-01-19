<?php
	require_once "php/db.php";
	use DB\DBAccess;

	session_start();

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
		$user = $_SESSION["username"];
	}
	else{
		header("location: autenticazione.php?url=compra-abbonamento.php");
		return;
	}

	$paginaHTML = file_get_contents("html/compra-abbonamento.html");

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		$data_scadenza_abbonamento = $connessione->doReadQuery("SELECT data_fine FROM utente WHERE username=?", "s", $user)[0]["data_fine"];

		$connessione->closeConnection();

		//Messaggio di abbonamento già presente
		$messaggio = "";
		if($data_scadenza_abbonamento != null){
			$messaggio = "<p>Ti ricordiamo che hai già un abbonamento attivo in scandenza il giorno " . $data_scadenza_abbonamento . ". Se compri un nuovo abbonamento la validità del precedente decadrà e il nuovo abbonamento sarà attivo immediatamente.</p>";
		} 

		$paginaHTML = str_replace("<messaggio_abbonamento_presente />", $messaggio, $paginaHTML);
		echo $paginaHTML;
	}
	else{
		$connessione->closeConnection();
		die("Si è verificato un errore nel server!");
	}
?>