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
        $queryUsername = $connessione->doReadQuery("SELECT username FROM utente");
        $connessione->closeConnection();

        $out .= "<option value=\"null\">Nessun allenatore</option>";
        foreach ($queryAllenatori as $row) {
            $out .= "<option value=\"" . $row["id"] . "\">" . $row["nome"] . " " . $row["cognome"] . "</option>";
        }
        $paginaHTML = str_replace("<selectAllenatore />", $out, $paginaHTML);
        $out = "";

        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == true) {
            $out .= "<label>Username cliente</label><select name=\"usernameScheda\">";
            foreach ($queryUsername as $row) {
                $out .= "<option value=\"" . $row["username"] . "\">" . $row["username"] . "</option>";
            }
            $out .= "</select>";
            $paginaHTML = str_replace("<selectUsername />", $out, $paginaHTML);
            $out = "";
        } else {
            $paginaHTML = str_replace("<selectUsername />", "", $paginaHTML);
        }
    } else {
        $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
    }
    echo $paginaHTML;
} else {
    header("location: autenticazione.php?url=inserimentoAllenamento.php&msg=1");
    return;
}

?>