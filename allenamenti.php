<?php
	require_once "db.php";
	use DB\DBAccess;

	if (!isset($_SESSION['numeroPagine'])) {
		$_SESSION['numeroPagine'] = 1;
	}
	$pagina = isset($_GET['pagina'])? $_GET['pagina'] : 1;
	$numeroPagine = $_SESSION['numeroPagine'];

	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	if ($connessioneOK) {
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){ // cliente = 0, admin = 1
			$tipoUtente = (($connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"]))[0]['isAdmin'])? 1 : 0;
			$utente = $_SESSION['username'];
		} else {
			$tipoUtente = 2;
			$utente = "admin"; //"";
		}

		$precedente = 0;
		$attuale = 0;
		if (isset($_POST['segui'])) {

			if ($_POST['segui'] == "seguire") {
				$connessione->doWriteQuery("INSERT INTO utente_allenamento(username_utente, id_allenamento) VALUES (?, ?)", "si", $utente, $_POST['id']);
			} else {
				$connessione->doWriteQuery("DELETE FROM utente_allenamento WHERE id_allenamento = ?", "i", $_POST['id']);
			}
			$attuale = $_POST['id'];
			unset($_POST['segui']);
		} elseif (isset($_POST['elimina'])) {
			$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $_POST['id']);
			$precedente = $_POST['idPrecedente'];
			unset($_POST['elimina']);
		}

		$numeroAllenamentiPerPagina = 5;
		$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;

		$queryAllenamentiResult = $connessione->doReadQuery("SELECT allenamento.id, nome, descrizione, allenamento.username_utente, data_creazione, Followers from allenamento LEFT JOIN (SELECT id_allenamento AS id, COUNT(id_allenamento) as Followers FROM utente_allenamento GROUP BY id_allenamento) AS TabellaFollowers USING(id) ORDER BY Followers DESC LIMIT ?, ?", "ii", $startRow, $numeroAllenamentiPerPagina);
		$queryPagineResult = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento");

		if ($tipoUtente != 2) {
			$content = "<form action='' method='post'><input type='hidden' name='isAdmin' value='" . $tipoUtente . "' readonly/><button type='submit'>Crea allenamento</button></form>";
		} else {
			$content = "<a href='autenticazione.php?url=allenamenti.php?pagina=" . $pagina . "'>Effettua l'autenticazione</a>";
		}
		$copyContent = $content;
		$itPrecedente = -1;

		foreach ($queryAllenamentiResult as $row) {
			$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = ?", "i", $row['id']);
			$numeroEsercizi = count($queryDettaglioAllenamentoResult);
			if ($precedente == $itPrecedente) {
				$content .= "<div><p>Allenamento eliminato!</p></div>";
			}
			$content .= '<div class="allenamento"><h2>' . $row['nome'] . '</h2><p>' . $row['descrizione'] . '</p><p>Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';
			if ($numeroEsercizi == 1) {
				$content .= 'o';
			}
			$content .= ', tra cui esercizi come ' . $queryDettaglioAllenamentoResult[0]['nome'];
			if ($numeroEsercizi > 1) {
				$j = 1;
				for (; $j <= $numeroEsercizi - 3 && $j <= 3; $j++) {
					$content .= ', ' . $queryDettaglioAllenamentoResult[$j]['nome'];
				}
				$content .= ' e ' . $queryDettaglioAllenamentoResult[$j]['nome'];
			}
			$content .= '.</p><ul><li>' . $row['username_utente'] . '</li><li>' . $row['data_creazione'] . '</li><li>' . $row['Followers'] . '</li></ul><a href="dettagli-allenamento.php?id=' . $row['id'] . '&url=allenamenti.php?pagina=' . $pagina . '&nomeBreadcrumb=Allenamenti">Apri nel dettaglio</a>';
			if ($tipoUtente == 2 || ($tipoUtente == 0 && $row['username_utente'] == $utente)) { // 1
				$content .= "<form action='' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><input type='hidden' name='isAdmin' value='" . $tipoUtente . "' readonly/><button type='submit'>Modifica allenamento</button></form>";
				$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='idPrecedente' value='" . $itPrecedente . "' readonly/><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='elimina' value='seguire'>Elimina allenamento</button></form>";
			} //else...
			if ($tipoUtente == 2) { // 0
				if (($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $row['id'], $utente))[0]['isFollowing'] == 0) {
					$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='segui' value='seguire'>Segui</button></form>";
					if ($row['id'] == $attuale) {
						$content .= "<div><p>Allenamento seguito!</p></div>";
					}
				} else {
					$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='segui' value='nonSeguire'>Smetti di seguire</button></form>";
					if ($row['id'] == $attuale) {
						$content .= "<div><p>Allenamento non seguito!</p></div>";
					}
				}
			}
			$content .= "</div>";
			$itPrecedente = $row['id'];
		}
		$connessione->closeConnection();
		if ($content == $copyContent) {
			$content .= "<p>Sembra che non ci siano allenamenti!</p>";
		}
		$_SESSION['numeroPagine'] = ceil($queryPagineResult[0]['numeroAllenamenti'] / $numeroAllenamentiPerPagina);
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	$contentPagine = "<ul>";
	for ($i = 1; $i < $pagina; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}
	$contentPagine .= "<li>" . $pagina . "</li>";
	for ($i = $pagina + 1; $i <= $_SESSION['numeroPagine']; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}
	$contentPagine .= "</ul>";

	$paginaHTML = str_replace("<allenamenti/>", $content, file_get_contents("allenamenti.html"));
	echo str_replace("<pagine/>", $contentPagine, $paginaHTML);
?>