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
	$tipoUtente = 2;
	$utente = "";
	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();
	if ($connessioneOK) {
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){ // cliente = 0, admin = 1
			$tipoUtente = $_SESSION["isAdmin"];
			$utente = $_SESSION['username'];
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
		$numeroAllenamentiPerPagina = 6;
		$startRow = $pagina * $numeroAllenamentiPerPagina - $numeroAllenamentiPerPagina;
		$queryAllenamentiResult = $connessione->doReadQuery("SELECT allenamento.id, nome, descrizione, allenamento.username_utente, data_creazione, Followers FROM allenamento LEFT JOIN (SELECT id_allenamento AS id, COUNT(id_allenamento) as Followers FROM utente_allenamento GROUP BY id_allenamento) AS TabellaFollowers USING(id) ORDER BY Followers DESC LIMIT ?, ?", "ii", $startRow, $numeroAllenamentiPerPagina);
		$queryPagineResult = $connessione->doReadQuery("SELECT COUNT(*) AS numeroAllenamenti FROM allenamento");
		$content = "";
		$copyContent = $content;
		$itPrecedente = -1;
		foreach ($queryAllenamentiResult as $row) {
			$queryDettaglioAllenamentoResult = $connessione->doReadQuery("SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM esercizio WHERE id_allenamento = ?", "i", $row['id']);
			$numeroEsercizi = count($queryDettaglioAllenamentoResult);
			if ($precedente == $itPrecedente) {
				$content .= "<div><p>Allenamento eliminato!</p></div>";
			}
			$content .= '<article id="' . $row['id'] . '"><h3>' . $row['nome'] . '</h3><p>' . $row['descrizione'] . '. Questo allenamento comprende ' . $numeroEsercizi . ' esercizi';
			if ($numeroEsercizi == 1) {
				$content .= 'o';
			}
			if($numeroEsercizi > 0){
				$content .= ', tra cui esercizi come ' . $queryDettaglioAllenamentoResult[0]['nome'];
			}
			if ($numeroEsercizi > 1) {
				$j = 1;
				for (; $j <= $numeroEsercizi - 3 && $j <= 3; $j++) {
					$content .= ', ' . $queryDettaglioAllenamentoResult[$j]['nome'];
				}
				$content .= ' e ' . $queryDettaglioAllenamentoResult[$j]['nome'];
			}
			$content .= '.</p><ul><li>Di ' . $row['username_utente'] . '</li><li>Creato il ' . $row['data_creazione'] . '</li><li>Seguito da ' . ($row['Followers'] == null ? 0 : $row['Followers']) . ' person' . ($row['Followers'] == 1 ? 'a' : 'e') . '</li></ul><div class="bottoni-allenamenti"><ul><li><a href="dettagli-allenamento.php?id=' . $row['id'] . '&nomeBreadcrumb=Allenamenti">Apri nel dettaglio</a></li>';
			
			if ($tipoUtente == 1 || ($tipoUtente == 0 && $row['username_utente'] == $utente)) {
				$content .= "<li><a href='modificaAllenamento.php?id=" . $row['id'] . "'>Modifica allenamento</a></li></ul>";
				$content .= "<form action='allenamenti.php?pagina=" . $pagina . "#" . $precedente . "' method='post'><input type='hidden' name='idPrecedente' value='" . $itPrecedente . "' readonly/><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button name='elimina' value='seguire'>Elimina allenamento</button></form>";
			} elseif ($tipoUtente == 0) {
				if ($connessione->doReadQuery("SELECT COUNT(*) AS isFollowing FROM utente_allenamento WHERE id_allenamento = ? AND username_utente = ?", "is", $row['id'], $utente)[0]['isFollowing'] == 0) {
					$content .= "</ul><form action='allenamenti.php?pagina=" . $pagina . "#" . $precedente . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button name='segui' value='seguire'>Segui</button></form></div>";				
					if ($row['id'] == $attuale) {
						$_SESSION['attuale'] = 0;
						$content .= "<p class='allenamento-avviso'>Hai smesso di seguire l'allenamento!</p>";
					}
				} else {
					$content .= "</ul><form action='allenamenti.php?pagina=" . $pagina . "#" . $precedente . "' method='post'><input type='hidden' name='id' value='" . $row['id'] . "' readonly/><button name='segui' value='nonSeguire'>Smetti di seguire</button></form></div>";
					
					if ($row['id'] == $attuale) {
						$_SESSION['attuale'] = 0;
						$content .= "<p class='allenamento-avviso'>Hai iniziato di seguire l'allenamento!</p>";
					}
				}
			}

			$content .= "</article>";
			$itPrecedente = $row['id'];
		}
		$connessione->closeConnection();
		if ($precedente == $itPrecedente) {
			$content .= "<article><p>Allenamento eliminato!</p></article>";
		}
		if ($content == $copyContent) {
			$content .= "<p class='allenamento-avviso'>Sembra che non ci siano allenamenti!</p>";
		}
		$_SESSION['numeroPagine'] = ceil($queryPagineResult[0]['numeroAllenamenti'] / $numeroAllenamentiPerPagina);
		$numeroPagine = $_SESSION['numeroPagine'];
	} else {
		$content = "<p class='allenamento-avviso'>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}
	$init = "";
	if ($_SESSION["loggedin"]) {
		$init = "<a href='inserimentoAllenamento.php'>Crea allenamento</a>";
	} else {
		$init = "<a href='autenticazione.php?url=allenamenti.php?pagina=" . $pagina . "'>Effettua l'autenticazione</a>";
	}

	$minNavPag = 2;
	$minScorri = $minNavPag <= $pagina? $minNavPag : $pagina - 1;

	$contentPagine = "";
	/*for ($i = 1; $i < (($pagina - $minScorri > 0) ? $pagina - $minScorri : 1) && $i < $minNavPag; $i++) {
		$j = $ + 1;
		if ($j < $pagina - $minScorri > 0 ? $pagina - $minScorri : 1 && $j < $minNavPag) {
	   		$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
		} else {
	   		$contentPagine .= "<li id='fine-pagine-iniziali'><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	    }
	}*/
	for ($i = $pagina - $minScorri > 0 ? $pagina - $minScorri : 1; $i < $pagina; $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}
	$contentPagine .= "<li id='currentLink'>" . $pagina . "</li>";
	for ($i = $pagina + 1; $i <= ($minNavPag + $pagina <= $numeroPagine ? $minNavPag + $pagina : $numeroPagine); $i++) {
	   	$contentPagine .= "<li><a href='allenamenti.php?pagina=" . $i . "'>" . $i . "</a></li>";
	}
	echo str_replace("<bottone-iniziale-destra />", $init, str_replace("<pagine />", $contentPagine, str_replace("<allenamenti />", $content, file_get_contents("html/allenamenti.html"))));
?>