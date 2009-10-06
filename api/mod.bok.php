<?php

include_once('../config.php');

if (!$config['moduler']['dvd']['aktiv']) {
  exit;
}

include_once('../include/functions.php');

if (!empty($_GET['artist'])) {
	
	if ($config['libraries'][$_GET['bib']]['sru']) {
			$q = masser_input($_GET['artist']);
			$qu = urlencode($q);
			$query = '(dc.author=' . $qu . '+or+dc.subject=' . $qu . ')+not+(dc.title=lydopptak+or+dc.title=video)';
			echo(sru_search($query, $_GET['bib'], $config['moduler']['dvd']['antall'], true));
	} else {
		echo('<p>Z39.50 - kommer...</p>');	
	}
	
}

?>