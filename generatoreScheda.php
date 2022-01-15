<?php
    require_once "db.php";
    use DB\DBAccess;
    session_start();

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){

        $nome = $_POST["nomeScheda"];
        $descrizione = $_POST["descrizioneScheda"];
        if(isset($_POST["usernameScheda"])){
            $username = $_POST["usernameScheda"];
        } else {
            $username = $_SESSION["username"];
        }
        $data = date("Y-m-d");
        $trainer = $_POST["allenatoreScheda"];
        
        $connessione = new DBAccess();
        $connessioneOK = $connessione->openDBConnection();
        
        if ($connessioneOK) {
            $connessione->doWriteQuery("INSERT INTO allenamento(nome, descrizione, username_utente, data_creazione, id_personal_trainer)
        VALUES(?,?,?,?,?)", "sssss",
        $nome, $descrizione, $username, $data, $trainer);
        $idSchedaQuery = $connessione->doReadQuery("SELECT max(id) as maxID FROM allenamento WHERE username_utente = ?","s",$username);
        $idScheda= $idSchedaQuery[0]["maxID"];
        $connessione->closeConnection();
        header("location: modificaAllenamento.php?id=".$idScheda);
        } else {
            $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
        }
    } else {
        header("location: autenticaione.php");
    }
?>