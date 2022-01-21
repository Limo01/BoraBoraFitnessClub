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

        $connessione->closeConnection();

        $confermaModifiche = "<a href=\"dettagli-allenamento.php?id=".$_GET["id"]."&nomeBreadcrumb=Allenamenti\">Conferma modifiche</a>";
        $paginaHTML = str_replace("<confermaModifiche />",$confermaModifiche,$paginaHTML);

        if(($schedaQuery != null) && ($_SESSION["isAdmin"] || $schedaQuery[0]["username_utente"] == $_SESSION["username"])){
            //form aggiungi esercizio
            $aggiungiEsercizio = 
            "<form id=\"aggiungiEsercizioForm\" action=\"php/generatoreScheda.php?id=". $_GET["id"] ."\", method=\"post\">
                <label>Nome</label>
                <input type=\"text\" name=\"nomeEsercizio\" required>

                <label>Descrizione</label>
                <input type=\"textarea\" name=\"descrizioneEsercizio\">
                
                <label>Peso</label>
                <input type=\"number\" name=\"pesoEsercizio\" min=\"0\" value=\"0\" step=\"0.1\">
                
                <label>Serie</label>
                <input type=\"number\" name=\"serieEsercizio\" min=\"0\" value=\"0\">
                
                <label>Ripetizioni</label>
                <input type=\"number\" name=\"ripetizioniEsercizio\" min=\"0\" value=\"0\">

                <label>Durata</label>
                <input type=\"time\" name=\"durataEsercizio\" min=\"0\", value=\"0:0:0\", step=\"1\">
                
                <button name=\"aggiungiEsercizioSubmit\">Aggiungi</button>
            </form>";
            $paginaHTML = str_replace("<insertEsercizio />",$aggiungiEsercizio,$paginaHTML);

            //form elimina esercizio
            $optionEsercizio = "<form id=\"eliminaEsercizioForm\" action=\"php/generatoreScheda.php?id=".$_GET["id"]."\", method=\"post\"><select name=\"esercizioScheda\"><label>Seleziona esercizio</label>";
            foreach($eserciziQuery as $row){
                $optionEsercizio .= "<option value=\"" . $row["nome"] . "\">" . $row["nome"] . "</option>";
            }
            $optionEsercizio .= "</select><button name=\"eliminaEsercizioSubmit\">Elimina</button></form>";
            $paginaHTML = str_replace("<deleteEsercizio />",$optionEsercizio,$paginaHTML);
            
            //dati scheda
            $datiScheda = "<div class=\"dettagli-allenamento\">";
            $datiScheda .= "<h2>" . $schedaQuery[0]["nome"] . "</h2>"; 
            $datiScheda .= "<p>Descrizione: " . $schedaQuery[0]["descrizione"] ."</p>";
            $datiScheda .= "<p>Data creazione: " . $schedaQuery[0]["data_creazione"] . "</p>";
            
            foreach($eserciziQuery as $row){
                //TODO: non mostrare serie, durata ecc se valore è null
                $datiScheda .= "<article><h3>" . $row["nome"] ."</h3>";
                if($row["descrizione"] != null) {
                    $datiScheda .= "<p>" . $row["descrizione"] ."</p>";
                }
                
                $datiScheda .= "<ul>";
                if($row["peso"] != null){
                    $datiScheda .= "<li>Peso: " . $row["peso"] . "</li>";
                } else {
                    $datiScheda .= "<li>Senza usare pesi</li>";
                }
                if($row["serie"] != null){
                    $datiScheda .= "<li>Serie: " . $row["serie"] . "</li>";
                }
                if($row["ripetizioni"] != null){
                    $datiScheda .= "<li>Ripetizioni: " . $row["ripetizioni"] . "</li>";
                }
                if($row["durata"] != null){
                    $datiScheda .= "<li>Durata: " . $row["durata"] . "</li>";
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
    //TODO: DA CHE PAGINA ARRIVO?
    header("location: autenticazione.php?");
}

?>