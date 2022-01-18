<?php
    require_once "db.php";
    use DB\DBAccess;
    session_start();
    if (isset($_POST['creaSchedaSubmit'])) {
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {

            $nome = $_POST["nomeScheda"];
            $descrizione = $_POST["descrizioneScheda"];
            if (isset($_POST["usernameScheda"])) {
                $username = $_POST["usernameScheda"];
            } else {
                $username = $_SESSION["username"];
            }
            $data = date("Y-m-d");
            $trainer = $_POST["allenatoreScheda"];

            $connessione = new DBAccess();
            $connessioneOK = $connessione->openDBConnection();

            if ($connessioneOK) {
                $connessione->doWriteQuery(
                    "INSERT INTO allenamento(nome, descrizione, username_utente, data_creazione, id_personal_trainer)
            VALUES(?,?,?,?,?)",
                    "sssss",
                    $nome,
                    $descrizione,
                    $username,
                    $data,
                    $trainer
                );
                $idSchedaQuery = $connessione->doReadQuery("SELECT max(id) as maxID FROM allenamento WHERE username_utente = ?", "s", $username);
                $idScheda = $idSchedaQuery[0]["maxID"];
                $connessione->closeConnection();
                header("location: ../modificaAllenamento.php?id=" . $idScheda);
            } else {
                $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
            }
        } else {
            header("location: ../autenticaione.php");
        }
    } elseif (isset($_POST['eliminaEsercizioSubmit'])) {
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
            $connessione = new DBAccess();
            $connessioneOK = $connessione->openDBConnection();

            if ($connessioneOK) {
                $is_adminQuery = $connessione->doReadQuery("SELECT is_admin from utente where username=?","s",$_SESSION["username"]);
                $schedaQuery = $connessione->doReadQuery("SELECT * from allenamento where id=?", "i", $_GET["id"]);
                if(($schedaQuery != null) && ($is_adminQuery[0]["is_admin"] == true || $schedaQuery[0]["username_utente"] == $_SESSION["username"])){
                    $idScheda = $_GET["id"];
                    $nomeEsercizio = $_POST["esercizioScheda"];
                    $connessione->doWriteQuery("DELETE FROM allenamento_esercizio WHERE id_allenamento = ? AND nome_esercizio = ?","is",$idScheda,$nomeEsercizio);
                    $connessione->closeConnection();
                    header("location: ../modificaAllenamento.php?id=".$_GET["id"]);
                } else {
                    echo "non puoi accedere a questa pagina";
                }  
            } else {
                $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
            }
        } else {
            header("location: ../autenticaione.php");
        }
        
    } elseif (isset($_POST['aggiungiEsercizioSubmit'])){
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
            $connessione = new DBAccess();
            $connessioneOK = $connessione->openDBConnection();

            if ($connessioneOK) {
                $is_adminQuery = $connessione->doReadQuery("SELECT is_admin from utente where username=?","s",$_SESSION["username"]);
                $schedaQuery = $connessione->doReadQuery("SELECT * from allenamento where id=?", "i", $_GET["id"]);
                if(($schedaQuery != null) && ($is_adminQuery[0]["is_admin"] == true || $schedaQuery[0]["username_utente"] == $_SESSION["username"])){
                    $idScheda = $_GET["id"];
                    $nomeEsercizio = $_POST["nomeEsercizio"];
                    $peso = $_POST["pesoEsercizio"] == 0 ? null : $_POST["pesoEsercizio"];
                    $ripetizioni = $_POST["ripetizioniEsercizio"] == 0 ? null : $_POST["ripetizioniEsercizio"];
                    $serie = $_POST["serieEsercizio"] == 0 ? null : $_POST["serieEsercizio"];
                    $durata = $_POST["durataEsercizio"] == 0 ? null : $_POST["durataEsercizio"];

                    $esercizioQuery = $connessione->doReadQuery("SELECT nome from esercizio where nome=?","s",$nomeEsercizio);
                    if($esercizioQuery == null){
                        $connessione->doWriteQuery("INSERT into esercizio(nome) values(?)","s",$nomeEsercizio);
                    }
                    $connessione->doWriteQuery("INSERT INTO allenamento_esercizio(id_allenamento,nome_esercizio,peso,ripetizioni,serie,durata) values(?,?,?,?,?,?)","isdiid",$idScheda,$nomeEsercizio,$peso,$ripetizioni,$serie,$durata);

                    $connessione->closeConnection();
                    header("location: ../modificaAllenamento.php?id=".$_GET["id"]);
                } else {
                    echo "non puoi accedere a questa pagina";
                }  
            } else {
                $out = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
            }
        } else {
            header("location: ../autenticaione.php");
        }
    }
?>