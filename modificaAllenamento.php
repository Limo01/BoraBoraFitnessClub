<!-- 
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
require_once "db.php";
use DB\DBAccess;
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $paginaHTML = file_get_contents("modificaAllenamento.html");
    $connessione = new DBAccess();
    $connessioneOK = $connessione->openDBConnection();
    if ($connessioneOK) {
        $isAdminQuery = $connessione->doReadQuery("SELECT isAdmin from utente where username=?","s",$_SESSION["username"]);
        $schedaQuery = $connessione->doReadQuery("SELECT * from allenamento where id=?", "i", $_GET["id"]);
        $eserciziQuery = $connessione->doReadQuery("SELECT * from allenamento_esercizio where id_allenamento=?", "i", $_GET["id"]);

        $connessione->closeConnection();
        if(($schedaQuery != null) && ($isAdminQuery[0]["isAdmin"] == true || $schedaQuery[0]["username_utente"] == $_SESSION["username"])){
            //form aggiungi esercizio
            $aggiungiEsercizio = 
            "<form id=\"aggiungiEsercizioForm\" action=\"generatoreScheda.php?id=". $_GET["id"] ."\", method=\"post\">
                <label>Nome</label>
                <input type=\"text\" name=\"nomeEsercizio\" required>
                
                <label>Peso</label>
                <input type=\"number\" name=\"pesoEsercizio\" min=\"0\" value=\"0\" step=\"0.1\">
                
                <label>Serie</label>
                <input type=\"number\" name=\"serieEsercizio\" min=\"0\" value=\"0\">
                
                <label>Ripetizioni</label>
                <input type=\"number\" name=\"ripetizioniEsercizio\" min=\"0\" value=\"0\">

                <label>Durata</label>
                <input type=\"number\" name=\"durataEsercizio\" min=\"0\", value=\"0\", step=\"0.01\">
                
                <input type=\"submit\" value=\"Aggiungi\" name=\"aggiungiEsercizioSubmit\">
            </form>";
            $paginaHTML = str_replace("<insertEsercizio/>",$aggiungiEsercizio,$paginaHTML);

            //form elimina esercizio
            $optionEsercizio = "<form id=\"eliminaEsercizioForm\" action=\"generatoreScheda.php?id=".$_GET["id"]."\", method=\"post\"><select name=\"esercizioScheda\"><label>Seleziona esercizio</label>";
            foreach($eserciziQuery as $row){
                $optionEsercizio .= "<option value=\"" . $row["nome_esercizio"] . "\">" . $row["nome_esercizio"] . "</option>";
            }
            $optionEsercizio .= "<input type=\"submit\" value=\"Elimina\" name=\"eliminaEsercizioSubmit\"></select></form>";
            $paginaHTML = str_replace("<deleteEsercizio/>",$optionEsercizio,$paginaHTML);
            
            //dati scheda
            $datiScheda = "<div id=\"abbonamenti\">";
            $datiScheda .= "<h2>" . $schedaQuery[0]["nome"] . "</h2>"; 
            $datiScheda .= "<p>Descrizione: " . $schedaQuery[0]["descrizione"] ."</p>";
            $datiScheda .= "<p>Data creazione: " . $schedaQuery[0]["data_creazione"] . "</p>";
            
            foreach($eserciziQuery as $row){
                //TODO: non mostrare serie, durata ecc se valore è null
                $datiScheda .= "<div class=\"esercizio\"><h2>" . $row["nome_esercizio"] ."</h2><ul><li>peso:" . 
                $row["peso"] . "</li><li>Serie: ". $row["serie"] ."</li><li>ripetizioni: ". $row["ripetizioni"] ."</li><li>durata: ". $row["durata"] ."</li></ul></div>";
            }
            
            $datiScheda .= "</div>";
            $paginaHTML = str_replace("<datiScheda/>",$datiScheda,$paginaHTML);
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