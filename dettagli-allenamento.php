<?php
	session_start();
	require_once "php/db.php";
	use DB\DBAccess;
	$nomeBreadcrumb = isset($_GET['url'])? (isset($_GET['nomeBreadcrumb'])? $_GET['nomeBreadcrumb'] : strtoupper(basename($_GET['url'])[0]) . substr(basename($_GET['url']), 1)) : "Allenamenti";
	$referer = isset($_GET['url'])? $_GET['url'] : "allenamenti.php";
	$tornaIndietro = "<a href='" . $referer . "'>Torna indietro</a>";
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
			$numeroAllenamenti = $connessione->doReadQuery("SELECT MAX(id) AS idMax FROM allenamento")[0]['idMax'];
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
				header('Location: dettagli-allenamento.php?id=' . $id . '&nomeBreadcrumb=' . $nomeBreadcrumb . '&url=' . $referer);
				return;
			} elseif (isset($_POST['elimina'])) {
				$connessione->doWriteQuery("DELETE FROM allenamento WHERE id = ?", "i", $id);
				$_SESSION['changes'] = true;
				header('Location: dettagli-allenamento.php?id=' . $id . '&nomeBreadcrumb=' . $nomeBreadcrumb . '&url=' . $referer);
				return;
			}
			$queryOverviewAllenamentoResult = $connessione->doReadQuery("SELECT id, nome, descrizione, allenamento.username_utente, data_creazione, COUNT(id) AS Followers FROM allenamento LEFT JOIN utente_allenamento ON id = id_allenamento WHERE id = ?", "i", $id);
			$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM esercizio WHERE id_allenamento = ?", "i", $id);

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
				$content .= '.</p><ul id="specifiche-utente-dettaglio-allenamento"><li>Di ' . $queryOverviewAllenamentoResult[0]['username_utente'] . '</li><li>Creato il ' . $queryOverviewAllenamentoResult[0]['data_creazione'] . '</li><li>Seguito da ' . ($queryOverviewAllenamentoResult[0]['Followers'] == null ? 0 : $queryOverviewAllenamentoResult[0]['Followers']) . ' person' . ($queryOverviewAllenamentoResult[0]['Followers'] == 1 ? 'a' : 'e') . '</li></ul><div class="bottoni-allenamenti">';
				if ($tipoUtente == 1 || ($tipoUtente == 0 && $queryOverviewAllenamentoResult[0]['username_utente'] == $utente)) {
					$content .= "<ul><li><a href='modificaAllenamento.php?id=" . $id . "'>Modifica allenamento</a></li></ul>";
					$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "&url=" . $referer . "' method='post'><button name='elimina'>Elimina allenamento</button></form>";
				} elseif ($tipoUtente == 0) {
					if ($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $id, $utente)[0]['isFollowing'] == 0) {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "&url=" . $referer . "' method='post'><button name='segui' value='seguire'>Segui</button></form>";

						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<p class='allenamento-avviso'>Hai smesso di seguire l'allenamento!</p>";
						}
					} else {
						$content .= "<form action='dettagli-allenamento.php?id=" . $id . "&nomeBreadcrumb=" . $nomeBreadcrumb . "&url=" . $referer . "' method='post'><button name='segui' value='nonSeguire'>Smetti di seguire</button></form>";

						if ($id == $changes) {
							$_SESSION['changes'] = false;
							$content .= "<p class='allenamento-avviso'>Hai iniziato a seguire l'allenamento!</p>";
						}
					}
				}

				$content .= "</div><div class='dettagli-allenamento'>";

				foreach($queryDettaglioAllenamentoResult as $row){
	                $content .= "<article><h3>" . $row["nome"] ."</h3>";
	                if($row["descrizione"] != null) {
	                    $content .= "<p>" . $row["descrizione"] ."</p>";
	                }
	                
	                $content .= "<ul>";
	                if($row["peso"] != null){
	                    $content .= "<li>Con +" . $row["peso"] . " di peso</li>";
	                } else {
	                    $content .= "<li>Senza usare pesi</li>";
	                }
	                if($row["serie"] != null){
	                    $content .= "<li>" . $row["serie"] . " serie</li>";
	                }
	                if($row["ripetizioni"] != null){
	                    $content .= "<li>" . $row["ripetizioni"] . " ripetizioni</li>";
	                }
	                if($row["durata"] != null){
	                    $content .= "<li>Durata di " . $row["durata"] . "</li>";
	                }
	                $content .= "</ul>";
	                $content .= "</article>";
            	}
            	
				$content .= "</div>";
			} elseif ($changes) {
				$content .= "<h2 id='titolo-dettagli-allenamento'>Allenamento eliminato</h2><p class='allenamento-avviso'>Allenamento eliminato!</p>";
				$_SESSION['changes'] = false;
			} else {
				$content .= "<h2 id='titolo-dettagli-allenamento'>Allenamento inesistente</h2><p class='allenamento-avviso'>Sembra che questo allenamento non esista!</p>";
			}
			$connessione->closeConnection();
		} else {
			$content .= "<h2 id='titolo-dettagli-allenamento'>Errore dei nostri sistemi</h2><p class='allenamento-avviso'>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
		}
	} else {
		$content .= "<h2 id='titolo-dettagli-allenamento'>Nessun allenamento</h2><p class='allenamento-avviso'>Nessun allenamento indicato!</p>";
	}

	$strtbtn = "";
	if ($tipoUtente != 2) {
		$strtbtn = "<a href='inserimentoAllenamento.php'>Crea allenamento</a>";
	} else {
		$strtbtn = "<a href='autenticazione.php?url=dettagli-allenamento.php?nomeBreadcrumb=Allenamenti&id=" . $id . "'>Effettua l'autenticazione</a>";
	}

	$paginePrecedenteProssimo = "";

	if ($id > 1) {
		$paginePrecedenteProssimo .= "<li><a href='dettagli-allenamento.php?id=" . ($id - 1) . "&nomeBreadcrumb=" . $nomeBreadcrumb . "&url=" . $referer . "'>Precedente</a></li>";
	}
	if ($id < $numeroAllenamenti) {
		$paginePrecedenteProssimo .= "<li><a href='dettagli-allenamento.php?id=" . ($id + 1) . "&nomeBreadcrumb=" . $nomeBreadcrumb . "&url=" . $referer . "'>Prossimo</a></li>";
	}

	echo str_replace("<paginePrecedenteProssimo />", $paginePrecedenteProssimo, str_replace("<bottone-iniziale-destra />", $strtbtn, str_replace("<bottone-iniziale />", $tornaIndietro, str_replace("<dettagli-allenamento />", $content, str_replace("<genitore-breadcrumb />", "<a href='" . $referer . "'>" . $nomeBreadcrumb . "</a>", file_get_contents("html/dettagli-allenamento.html"))))));
?>