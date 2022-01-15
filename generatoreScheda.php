<!-- id int primary key auto_increment, 
    nome varchar(100) not null,
    descrizione text,
    username_utente varchar(50)
        references utente(username)
            on delete set null
            on update cascade,
    data_creazione date default (current_date),
    id_personal_trainer int
        references personal_trainer(id)
            on delete set null
            on update cascade -->

<?php
    require_once "db.php";
    use DB\DBAccess;
    session_start();
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
        echo "inserito";
    } else {
        $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
    }
?>