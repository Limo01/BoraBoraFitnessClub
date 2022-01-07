<?php
	require_once "db-prepared.php";
	use DB\DBAccess;

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if($connessioneOK){
		echo "funziona </br>";
	}

	$result= $connessione->doReadQuery("SELECT * FROM cliente WHERE username=? and password=?", "ss", "user", "user");
	
	print_r($result);
	echo "<br>" . count($result);
	
	//echo $connessione->doWriteQuery("INSERT INTO `allenamento`(`nome`, `descrizione`, `username_cliente`, `data_creazione`, `id_personal_trainer`) VALUES (?,?,?,?,?)", "ssssi", "prova", "descrizione", 'user', '2002-08-10', 1);
	
	//echo $connessione->doWriteQuery("INSERT INTO `sala`(`nome`) VALUES (?)", "s", "pippo");

	echo "<br>";
	if(!$connessione->isUsernameCorrect("user"))
		echo "persona falsa";
	else
		echo "persona buona";

	echo "<br>";
	if($connessione->isPasswordCorrect("user", "user"))
		echo "persona loggata";
	else
		echo "persona non loggata";
?>