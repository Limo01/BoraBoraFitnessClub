<?php
	require_once "db_sample.php";
	use DB\DBAccess;
	session_start();

    $connessione = new DBAccess();
    $connessioneOK = $connessione->openDBConnection();

    if ($connessioneOK) {
        $result = $connessione->doReadQuery("select * from cliente");
        $listaClienti = "";
        foreach ($result as $cliente) {
            $listaClienti .= '<dd>' . $cliente['username'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['password'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['nome'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['cognome'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['email'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['data_nascita'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['badge'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['entrate'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['numero_telefono'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['nome_abbonamento'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['data_inizio'] . '</dd>';
            $listaClienti .= '<dd>' . $cliente['data_fine'] . '</dd>';
        }
    } else {
        echo"<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
    }

    //echo $listaClienti;
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){
        echo $_SESSION["username"];
    }
    else{
        echo "not logged in";
    }
?>