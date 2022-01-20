<?php
	session_start();
	require_once "php/db.php";
	use DB\DBAccess;
	$nomeBreadcrumb = isset($_GET['url'])? (isset($_GET['nomeBreadcrumb'])? $_GET['nomeBreadcrumb'] : strtoupper(basename($_GET['url'])[0]) . substr(basename($_GET['url']), 1)) : "Allenamenti";
	$referer = isset($_GET['url'])? $_GET['url'] : "allenamenti.php";
	$init = "<a href='" . $referer . "'>Torna indietro</a>";
	$id = isset($_GET['id'])? $_GET['id'] : 0;
	$tipoUtente = 2;
	$utente = "";
	$content = "";
	$numeroAllenamenti = 0;
	
	if ($id > 0) {
		if ($nomeBreadcrumb == "Allenamenti") {
			$referer .= "#" . $id;
		}
		if (!isset($_SESSION['changes'])) {
			$_SESSION['changes'] = false;
		}
		$changes = $_SESSION['changes'];
		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();
		if ($connessioneOK) {
			$numeroAllenamenti = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento")[0]['numeroAllenamenti'];
			if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){ // cliente = 0, admin = 1
				$tipoUtente = $_SESSION["isAdmin"];
				$utente = $_SESSION['username'];
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

			if ($queryOverviewAllenamentoResult[0]['id'] != null) {
				$numeroEsercizi = count($queryDettaglioAllenamentoResult);
				$content .= '<h2 id="titolo-dettagli-allenamento">' . $queryOverviewAllenamentoResult[0]['nome'] . '</h2><p>' . $queryOverviewAllenamentoResult[0]['descrizione'] . '. Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';			
				if ($numeroEsercizi == 1) {
					$content .= 'o';
				}
				if($numeroEsercizi > 0){
					$content .= ', tra cui esercizi come ' . $queryDettaglioAllenamentoResult[0]['nome'];
				}		
				if ($numeroEsercizi > 1) {
					$i = 1;
					for (; $i <= $numeroEsercizi - 3 && $i <= 3; $i++) {
						$content .= ', ' . $queryDettaglioAllenamentoResult[$i]['nome'];
					}
					$content .= ' e ' . $queryDettaglioAllenamentoResult[$i]['nome'];
				}
				$content .= '.</p><ul id="specifiche-utente-dettaglio-allenamento"><li>' . $queryOverviewAllenamentoResult[0]['username_utente'] . '</li><li>' . $queryOverviewAllenamentoResult[0]['data_creazione'] . '</li><li>' . ($queryOverviewAllenamentoResult[0]['Followers'] == null ? 0 : $queryOverviewAllenamentoResult[0]['Followers']) . '</li></ul><div class="bottoni-allenamenti">';
				if ($tipoUtente == 1 || ($tipoUtente == 0 && $queryOverviewAllenamentoResult[0]['username_utente'] == $utente)) {
					$content .= "<ul><li><a href='modificaAllenamento.php?id=" . $id . "'>Modifica allenamento</a></li></ul>";
					$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button name='elimina' value='seguire'>Elimina allenamento</button></form>";
				} elseif ($tipoUtente == 0) {
					if ($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $id, $utente)[0]['isFollowing'] == 0) {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button name='segui' value='seguire'>Segui</button></form>";

						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<p class='allenamento-avviso'>Hai smesso di seguire l'allenamento!</p>";
						}
					} else {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "' method='post'><button name='segui' value='nonSeguire'>Smetti di seguire</button></form>";

						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<p class='allenamento-avviso'>Hai iniziato a seguire l'allenamento!</p>";
						}
					}
				}

				$content .= "</div><div class='dettagli-allenamento'>";

				foreach ($queryDettaglioAllenamentoResult as $esercizio) {
					$content .= '<article><h3>' . $esercizio['nome'] . '</h3><p>' . $esercizio['descrizione'] . '</p><ul><li>Con +' . $esercizio['peso'] . 'kg</li><li>' . $esercizio['serie'] . ' serie</li><li>' . $esercizio['ripetizioni'] . ' ripetizioni</li><li>' . ($esercizio['durata'] == null ? "Durata non specificata" : "Durata di " . $esercizio['durata']) . '</li></ul></article>';
				}
				$content .= "</div>";
			} elseif ($changes) {
				$content .= "<p class='allenamento-avviso'>Allenamento eliminato!</p>";
				$changes = false;
			} else {
				$content .= "<p class='allenamento-avviso'>Sembra che questo allenamento non esista!</p>";
			}
			$connessione->closeConnection();
		} else {
			$content .= "<p class='allenamento-avviso'>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
	} else {
		$content .= "<p class='allenamento-avviso'>Nessun allenamento indicato!</p>";
	}

	$initiate = "";
	if ($tipoUtente != 2) {
		$initiate = "<a href='inserimentoAllenamento.php'>Crea allenamento</a>";
	} else {
		$initiate = "<a href='autenticazione.php?url=dettaglio-allenamento.php?nomeBreadcrumb=Allenamenti&id=" . $id . "'>Effettua l'autenticazione</a>";
	}

	$pag = "";

	if ($id > 1) {
		$pag .= "<li><a href=''>Precedente" . ($id - 1) . "</a></li>";
	}
	if ($id < $numeroAllenamenti) {
		$pag .= "<li><a href=''>Precedente" . ($id + 1) . "</a></li>";
	}

	echo str_replace("<pagine />", $pag, str_replace("<bottone-iniziale-destra />", $initiate, str_replace("<bottone-iniziale />", $init, str_replace("<dettagli-allenamento />", $content, str_replace("<genitore-breadcrumb />", "<a href='" . $referer . "'>" . $nomeBreadcrumb . "</a>", file_get_contents("html/dettagli-allenamento.html"))))));
?>