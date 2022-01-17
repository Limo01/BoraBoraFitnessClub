<?php
	session_start();
	require_once "php/db.php";
	use DB\DBAccess;
	if (!isset($_SESSION['numeroPagine'])) {
		$_SESSION['numeroPagine'] = 1;
	}
	if (!isset($_SESSION['attuale'])) {
		$_SESSION['attuale'] = 0;
	}
	if (!isset($_SESSION['precedente'])) {
		$_SESSION['precedente'] = 0;
	}
	$pagina = isset($_GET['pagina'])? $_GET['pagina'] : 1;
	$numeroPagine = $_SESSION['numeroPagine'];
	$attuale = $_SESSION['attuale'];
	$precedente = $_SESSION['precedente'];
	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	if ($connessioneOK) {
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){ // cliente = 0, admin = 1
			$tipoUtente = $connessione->doReadQuery("SELECT isAdmin FROM utente WHERE username = ?", "s", $_SESSION["username"])[0]['isAdmin']? 1 : 0;
			$utente = $_SESSION['username'];
		} else {
			$tipoUtente = 2;
			$utente = "";
		}
		if (isset($_POST['segui'])) {
			$id = $_POST['id'];
			if ($_POST['segui'] == "seguire") {
				$connessione->doWriteQuery("INSERT INTO utente_allenamento(username_utente, id_allenamento) VALUES (?, ?)", "si", $utente, $id);
			} else {
				$connessione->doWriteQuery("DELETE FROM utente_allenamento WHERE id_allenamento = ?", "i", $id);
			}
			$_SESSION['attuale'] = $id;
			header('Location: allenamenti.php?pagina=' . $pagina);
			return;
		} elseif (isset($_POST['elimina'])) {
			$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $_POST['id']);
			$_SESSION['precedente'] = $_POST['idPrecedente'];
			header('Location: allenamenti.php?pagina=' . $pagina);
			return;
		}
		if (isset($_POST['cerca'])) {
			if (isset($_POST['titolo'])) {
				$_SESSION['titolo'] = $_POST['titolo'];
			}
			header('Location: allenamenti.php?pagina=' . $pagina);
			return;
		}
		$numeroAllenamentiPerPagina = 5;
		$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;
		//if (isset($_SESSION['titolo'])) {
			//echo $_SESSION['titolo'];
			//$queryAllenamentiResult = $connessione->doReadQuery("SELECT allenamento.id, nome, descrizione, allenamento.username_utente, data_creazione, Followers FROM allenamento LEFT JOIN (SELECT id_allenamento AS id, COUNT(id_allenamento) as Followers FROM utente_allenamento GROUP BY id_allenamento) AS TabellaFollowers USING(id) WHERE nome LIKE %?% OR ? LIKE %nome% ORDER BY Followers DESC LIMIT ?, ?", "ssii", $_SESSION['titolo'], $_SESSION['titolo'], $startRow, $numeroAllenamentiPerPagina);
		//} else {
			$queryAllenamentiResult = $connessione->doReadQuery("SELECT allenamento.id, nome, descrizione, allenamento.username_utente, data_creazione, Followers FROM allenamento LEFT JOIN (SELECT id_allenamento AS id, COUNT(id_allenamento) as Followers FROM utente_allenamento GROUP BY id_allenamento) AS TabellaFollowers USING(id) ORDER BY Followers DESC LIMIT ?, ?", "ii", $startRow, $numeroAllenamentiPerPagina);
		//}
		$queryPagineResult = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento");
		if ($tipoUtente != 2) {
			$content = "<a href='inserimentoAllenamento.php'>Crea allenamento</a>";
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
			$content .= '<div class="allenamento"><h3>' . $row['nome'] . '</h3><p>' . $row['descrizione'] . '</p><p>Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';
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
			$content .= '.</p><ul><li>' . $row['username_utente'] . '</li><li>' . $row['data_creazione'] . '</li><li>' . ($row['Followers'] == null ? 0 : $row['Followers']) . '</li></ul><a href="dettagli-allenamento.php?id=' . $row['id'] . '&nomeBreadcrumb=Allenamenti">Apri nel dettaglio</a>';
			if ($tipoUtente == 1 || ($tipoUtente == 0 && $row['username_utente'] == $utente)) {
				$content .= "<a href='modificaAllenamento.php?id=" . $row['id'] . "'>Modifica allenamento</a>";
				$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='idPrecedente' value='" . $itPrecedente . "' readonly/><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='elimina' value='seguire'>Elimina allenamento</button></form>";
			} elseif ($tipoUtente == 0) {
				if ($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $row['id'], $utente)[0]['isFollowing'] == 0) {
					$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='segui' value='seguire'>Segui</button></form>";				
					if ($row['id'] == $attuale) {
						$_SESSION['attuale'] = 0;
						$content .= "<p>Hai smesso di seguire l'allenamento!</p>";
					}
				} else {
					$content .= "<form action='allenamenti.php?pagina=" . $pagina . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button type='submit' name='segui' value='nonSeguire'>Smetti di seguire</button></form>";
					if ($row['id'] == $attuale) {
						$_SESSION['attuale'] = 0;
						$content .= "<p>Hai iniziato di seguire l'allenamento!</p>";
					}
				}
			}
			$content .= "</div>";
			$itPrecedente = $row['id'];
		}
		$connessione->closeConnection();
		if ($precedente == $itPrecedente) {
			$content .= "<div><p>Allenamento eliminato!</p></div>";
		}
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
	for ($i = $pagina + 1; $i <= $numeroPagine; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}
	$contentPagine .= "</ul>";
	echo str_replace("<pagine />", $contentPagine, str_replace("<allenamenti />", $content, file_get_contents("html/allenamenti.html")));
?>