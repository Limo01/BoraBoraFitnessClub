<?php
	session_start();
	require_once "php/db.php";
	use DB\DBAccess;
	$referer = $_SERVER['HTTP_REFERER'] != null? $_SERVER['HTTP_REFERER'] : "allenamenti.php";
	$basename = basename($referer, ".php");
	$nomeBreadcrumb = isset($_GET['nomeBreadcrumb'])? $_GET['nomeBreadcrumb'] : strtoupper($basename[0]) . substr($basename, 1);
	$content = "<a href='" . $referer . "'>Torna indietro</a>";
	$id = isset($_GET['id'])? $_GET['id'] : 0;
	if ($id > 0) {
		if (!isset($_SESSION['changes'])) {
			$_SESSION['changes'] = false;
		}
		$changes = $_SESSION['changes'];
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
				if ($_POST['segui'] == "seguire") {
					$connessione->doWriteQuery("INSERT INTO utente_allenamento(username_utente, id_allenamento) VALUES (?, ?)", "si", $utente, $id);
				} else {
					$connessione->doWriteQuery("DELETE FROM utente_allenamento WHERE id_allenamento = ?", "i", $id);
				}
				$_SESSION['changes'] = true;
				header('Location: dettagli-allenamento.php?id=' . $id . '&nomeBreadcrumb=' . $nomeBreadcrumb);
				return;
			} elseif (isset($_POST['elimina'])) {
				$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $id);
				$_SESSION['changes'] = true;
				header('Location: dettagli-allenamento.php?id=' . $id . '&nomeBreadcrumb=' . $nomeBreadcrumb);
				return;
			}
			$queryOverviewAllenamentoResult = $connessione->doReadQuery("SELECT id, nome, descrizione, allenamento.username_utente, data_creazione, COUNT(id) AS Followers FROM allenamento LEFT JOIN utente_allenamento ON id = id_allenamento WHERE id = ?", "i", $id);
			$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = ?", "i", $id);
			if ($tipoUtente == 2) {
				$content .= "<a href='autenticazione.php?url=dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=Autenticazione'>Effettua l'autenticazione</a>";
			}
			if ($queryOverviewAllenamentoResult[0]['id'] != null) {
				$numeroEsercizi = count($queryDettaglioAllenamentoResult);
				$content .= '<h2>' . $queryOverviewAllenamentoResult[0]['nome'] . '</h2><p>' . $queryOverviewAllenamentoResult[0]['descrizione'] . '</p><p>Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';			
				if ($numeroEsercizi == 1) {
					$content .= 'o';
				}
				$content .= ', tra cui esercizi come ' . $queryDettaglioAllenamentoResult[0]['nome'];		
				if ($numeroEsercizi > 1) {
					$i = 1;
					for (; $i <= $numeroEsercizi - 3 && $i <= 3; $i++) {
						$content .= ', ' . $queryDettaglioAllenamentoResult[$i]['nome'];
					}
					$content .= ' e ' . $queryDettaglioAllenamentoResult[$i]['nome'];
				}
				$content .= '.</p><ul><li>' . $queryOverviewAllenamentoResult[0]['username_utente'] . '</li><li>' . $queryOverviewAllenamentoResult[0]['data_creazione'] . '</li><li>' . ($queryOverviewAllenamentoResult[0]['Followers'] == null ? 0 : $queryOverviewAllenamentoResult[0]['Followers']) . '</li></ul>';
				if ($tipoUtente == 1 || ($tipoUtente == 0 && $queryOverviewAllenamentoResult[0]['username_utente'] == $utente)) {
					$content .= "<a href='modificaAllenamento.php?id=" . $id . "'>Modifica allenamento</a>";
					$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button type='submit' name='elimina' value='seguire'>Elimina allenamento</button></form>";
				} elseif ($tipoUtente == 0) {
					if ($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $id, $utente)[0]['isFollowing'] == 0) {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button type='submit' name='segui' value='seguire'>Segui</button></form>";
						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<div><p>Hai smesso di seguire l'allenamento!</p></div>";
						}
					} else {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button type='submit' name='segui' value='nonSeguire'>Smetti di seguire</button></form>";
						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<div><p>Hai iniziato di seguire l'allenamento!</p></div>";
						}
					}
				}
				foreach ($queryDettaglioAllenamentoResult as $esercizio) {
					$content .= '<div class="esercizio"><h2>' . $esercizio['nome'] . '</h2><p>' . $esercizio['descrizione'] . '</p><ul><li>' . $esercizio['peso'] . '</li<li>' . $esercizio['serie'] . '</li><li>' . $esercizio['ripetizioni'] . '</li><li>' . ($esercizio['durata'] == null ? "00:00:00" : $esercizio['durata']) . '</li></ul></div>';
				}
			} elseif ($changes) {
				$content .= "<p>Allenamento eliminato!</p>";
				$changes = false;
			} else {
				$content .= "<p>Sembra che questo allenamento non esista!</p>";
			}
			$connessione->closeConnection();
		} else {
			$content .= "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
	} else {
		$content .= "<p>Nessun allenamento indicato!</p>";
	}
	echo str_replace("<dettaglioAllenamento/>", $content, str_replace("<genitoreBreadcrumb/>", "<a href='" . $referer . "'>" . $nomeBreadcrumb . "</a>", file_get_contents("html/dettagli-allenamento.html")));
?>