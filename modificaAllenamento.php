<?php
require_once "php/db.php";
use DB\DBAccess;
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $paginaHTML = file_get_contents("html/modificaAllenamento.html");
    $connessione = new DBAccess();
    $connessioneOK = $connessione->openDBConnection();
    if ($connessioneOK) {
        $schedaQuery = $connessione->doReadQuery("SELECT * from allenamento where id=?", "i", $_GET["id"]);
        $eserciziQuery = $connessione->doReadQuery("SELECT * from esercizio where id_allenamento=?", "i", $_GET["id"]);
        $allenatoreQuery = $connessione->doReadQuery("SELECT * FROM personal_trainer");

        $connessione->closeConnection();

        if(($schedaQuery != null) && ($_SESSION["isAdmin"] || $schedaQuery[0]["username_utente"] == $_SESSION["username"])){
            //form aggiungi esercizio
            $aggiungiEsercizio = 
            "<form id=\"inserimentoEsercizio\" action=\"php/generatoreScheda.php?id=". $_GET["id"] ."\" method=\"post\">
                <label id=\"nomeEsercizioL\">Nome</label>
                <input id=\"nomeEsercizioI\" type=\"text\" name=\"nomeEsercizio\" required>

                <label id=\"descrizioneEsercizioL\">Descrizione</label>
                
                <textarea rows=\"3\" cols=\"30\" id=\"descrizioneEsercizioI\" name=\"descrizioneEsercizio\"></textarea>

                <label id=\"pesoEsercizioL\">Peso</label>
                <input id=\"pesoEsercizioI\" type=\"number\" name=\"pesoEsercizio\" min=\"0\" value=\"0\" step=\"0.1\">
                
                <label id=\"serieEsercizioL\">Serie</label>
                <input id=\"serieEsercizioI\" type=\"number\" name=\"serieEsercizio\" min=\"0\" value=\"0\">
                
                <label id=\"ripetizioniEsercizioL\">Ripetizioni</label>
                <input id=\"ripetizioniEsercizioI\" type=\"number\" name=\"ripetizioniEsercizio\" min=\"0\" value=\"0\">

                <label id=\"durataEsercizioL\">Durata</label>
                <input id=\"durataEsercizioI\" type=\"time\" name=\"durataEsercizio\" min=\"00:00:00\" value=\"00:00:00\" step=\"1\">
                
                <button id=\"buttonAddEsercizio\" name=\"aggiungiEsercizioSubmit\">Aggiungi</button>
            </form>";
            $paginaHTML = str_replace("<insertEsercizio />",$aggiungiEsercizio,$paginaHTML);

            //form elimina esercizio
            $optionEsercizio = "<form id=\"inserimentoForm\" action=\"php/generatoreScheda.php?id=".$_GET["id"]."\" method=\"post\"><label>Seleziona esercizio da eliminare</label><select name=\"esercizioScheda\">";
            foreach($eserciziQuery as $row){
                $optionEsercizio .= "<option value=\"" . $row["nome"] . "\">" . $row["nome"] . "</option>";
            }
            $optionEsercizio .= "</select><button id=\"buttonDelEsercizio\" name=\"eliminaEsercizioSubmit\">Elimina</button></form>";
            if($eserciziQuery != null) {
                $paginaHTML = str_replace("<deleteEsercizio />",$optionEsercizio,$paginaHTML);
            } else {
                $paginaHTML = str_replace("<deleteEsercizio />","",$paginaHTML);
            }
            
            //dati scheda
            
            $datiScheda = "<h2>" . $schedaQuery[0]["nome"] . "</h2>";
            
            if(isset($_GET["notifica"]) && $_GET["notifica"] == "1"){
                $datiScheda.= "<p class=\"notification\">Esercizio aggiunto con successo!</p>";
            } elseif (isset($_GET["notifica"]) && $_GET["notifica"] == "2"){
                $datiScheda.= "<p class=\"notification\">Esercizio rimosso con successo!</p>";
            }

            $datiScheda .= "<a href=\"dettagli-allenamento.php?id=".$_GET["id"]."&nomeBreadcrumb=Modifica+allenamento&url=modificaAllenamento.php?id=" . $_GET['id'] . "\">Torna ai dettagli</a>";

            if($schedaQuery[0]["descrizione"] != "") {
                $datiScheda .= "<p>" . $schedaQuery[0]["descrizione"] ."</p>";
            }
            $datiScheda .= "<p>Data creazione: " . $schedaQuery[0]["data_creazione"] . "</p>";
            if($schedaQuery[0]["id_personal_trainer"] != null){
                $datiScheda .= "<p>Allenatore di riferimento: " . $allenatoreQuery[$schedaQuery[0]["id_personal_trainer"]-1]["nome"] . " " . $allenatoreQuery[$schedaQuery[0]["id_personal_trainer"]-1]["cognome"] . "</p>";
            }
            $datiScheda .= "<div  class=\"dettagli-allenamento\">";
            foreach($eserciziQuery as $row){
                $datiScheda .= "<article><h3>" . $row["nome"] ."</h3>";
                if($row["descrizione"] != null) {
                    $datiScheda .= "<p>" . $row["descrizione"] ."</p>";
                }
                
                $datiScheda .= "<ul>";
                if($row["peso"] != null){
                    $datiScheda .= "<li>Con " . $row["peso"] . " chili</li>";
                } else {
                    $datiScheda .= "<li>Senza usare pesi</li>";
                }
                if($row["serie"] != null){
                    $datiScheda .= "<li>" . $row["serie"] . " serie</li>";
                }
                if($row["ripetizioni"] != null){
                    $datiScheda .= "<li>" . $row["ripetizioni"] . " ripetizioni</li>";
                }
                if($row["durata"] != null){
                    $datiScheda .= "<li>Durata di " . $row["durata"] . "</li>";
                }
                $datiScheda .= "</ul>";
                $datiScheda .= "</article>";
            }
            
            $datiScheda .= "</div>";
            $paginaHTML = str_replace("<datiScheda />",$datiScheda,$paginaHTML);
            echo $paginaHTML;
        } else {
            echo "non puoi accedere a questa pagina";
        }
        
    } else {
        $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
    }
    
} else {
    header("location: autenticazione.php?");
    return;
}

?>