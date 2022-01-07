<?php
	require_once "db.php";
	use DB\DBAccess;

	$paginaHTML = file_get_contents("dettaglio-allenamento.html");
	$connessione = new DBAccess();
	$connessioneOK = $connessione->openDBConnection();

	$id = $_GET['id'];
	$percorso = $_GET['percorso'];
	$nomeBreadcrumb = $_GET['nomeBreadcrumb'];
	$pagina = $_GET['pagina'];
	$breadcrumb = "<a href='" . $percorso . ".php?pagina=" . $pagina . "'>" . $nomeBreadcrumb . "</a>";
	
	$queryOverviewAllenamentoResult = "";
	$queryNumeroFollowersResult = "";
	$queryDettaglioAllenamentoResult = "";
	$content = "";

	$queryOverviewAllenamento = "SELECT nome, descrizione, username_cliente, data_creazione FROM allenamento WHERE id = " . $id;
	$queryNumeroFollowers = "SELECT COUNT(*) AS Followers FROM allenamento_esercizio WHERE id_allenamento = " . $id;
	$queryDettaglioAllenamento = "SELECT nome, descrizione, peso, serie, ripetizioni, durata FROM allenamento_esercizio JOIN esercizio ON allenamento_esercizio.nome_esercizio = esercizio.nome WHERE id_allenamento = " . $id;

	if ($connessioneOK) {
		$queryOverviewAllenamentoResult = $connessione->doReadQuery($queryOverviewAllenamento);
		$queryNumeroFollowersResult = $connessione->doReadQuery($queryNumeroFollowers);
		$queryDettaglioAllenamentoResult = $connessione->doReadQuery($queryDettaglioAllenamento);
		$connessione->closeConnection();

		$content = '<dl>';
		foreach ($queryOverviewAllenamentoResult as $overview) {
			$content .= '<dd>' . $overview['nome'] . '</dd>';
			$content .= '<dd>' . $overview['descrizione'] . '</dd>';
			$content .= '<dd>' . $overview['username_cliente'] . '</dd>';
			$content .= '<dd>' . $overview['data_creazione'] . '</dd>';
		}

		foreach ($queryNumeroFollowersResult as $followers) {
			$content .= '<dd>' . $followers['Followers'] . '</dd>';
		}

		foreach ($queryDettaglioAllenamentoResult as $esercizio) {
			$content .= '<dd>' . $esercizio['nome'] . '</dd>';
			$content .= '<dd>' . $esercizio['descrizione'] . '</dd>';
			$content .= '<dd>' . $esercizio['peso'] . '</dd>';
			$content .= '<dd>' . $esercizio['serie'] . '</dd>';
			$content .= '<dd>' . $esercizio['ripetizioni'] . '</dd>';
			$content .= '<dd>' . $esercizio['durata'] . '</dd>';
		}
		$content .= '</dl>';
	} else {
		$content = "<p>I sistemi sono al momento non disponibili, riprova più tardi!</p>";
	}

	$paginaHTML = str_replace("<genitoreBreadcrumb/>", $breadcrumb, $paginaHTML);
	$paginaHTML = str_replace("<dettaglioAllenamento/>", $content, $paginaHTML);
	echo $paginaHTML;
?>