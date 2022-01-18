<!-- create table allenamento(
    id int primary key auto_increment, 
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
            on update cascade
);

create table utente_allenamento(
    username_utente varchar(50)
        references utente(username)
            on delete cascade
            on update cascade,
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,

    primary key(username_utente, id_allenamento)
);

create table esercizio(
    nome varchar(100) primary key,
    descrizione text,
    nome_sala varchar(50)
        references sala(nome)
            on delete set null
            on update cascade
);

create table allenamento_esercizio(
    id_allenamento int
        references allenamento(id)
            on delete cascade
            on update cascade,
    nome_esercizio varchar(100)
        references esercizio(nome)
            on delete cascade
            on update cascade,
    peso decimal(5,1) default 0 check (peso >= 0),
    ripetizioni tinyint unsigned default 1,
    serie tinyint unsigned default 1,
    durata time,
    
    primary key(id_allenamento, nome_esercizio)
); -->

<?php
require_once "php/db.php";

use DB\DBAccess;

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $paginaHTML = file_get_contents("html/inserimentoAllenamento.html");
    $connessione = new DBAccess();
    $connessioneOK = $connessione->openDBConnection();
    $out = "";

    if ($connessioneOK) {
        $queryAllenatori = $connessione->doReadQuery("SELECT nome, cognome, id FROM personal_trainer");
        $queryis_admin = $connessione->doReadQuery("SELECT is_admin FROM utente WHERE username = ?", "s", $_SESSION["username"]);
        $queryUsername = $connessione->doReadQuery("SELECT username FROM utente");
        $connessione->closeConnection();

        foreach ($queryAllenatori as $row) {
            $out .= "<option value=\"" . $row["id"] . "\">" . $row["nome"] . " " . $row["cognome"] . "</option>";
        }
        $paginaHTML = str_replace("<selectAllenatore/>", $out, $paginaHTML);
        $out = "";

        if ($queryis_admin[0]["is_admin"] == true) {
            $out .= "<label>Username cliente</label><select name=\"usernameScheda\">";
            foreach ($queryUsername as $row) {
                $out .= "<option value=\"" . $row["username"] . "\">" . $row["username"] . "</option>";
            }
            $out .= "</select>";
            $paginaHTML = str_replace("<selectUsername/>", $out, $paginaHTML);
            $out = "";
        } else {
            $paginaHTML = str_replace("<selectUsername/>", "", $paginaHTML);
        }
    } else {
        $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
    }
    echo $paginaHTML;
} else {
    header("location: autenticazione.php?url=inserimentoAllenamento.php");
}

?>