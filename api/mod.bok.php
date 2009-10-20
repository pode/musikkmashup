<?php

include_once('../config.php');

if (!$config['moduler']['dvd']['aktiv']) {
  exit;
}

include_once('../include/functions.php');

if (!empty($_GET['artist'])) {
	
	$q = masser_input($_GET['artist']);
	$query = '';
	if ($config['libraries'][$_GET['bib']]['sru']) {
		$qu = urlencode($q);
		$query = "(dc.author=$qu+or+dc.subject=$qu)+not+(dc.title=lydopptak+or+dc.title=video+or+dc.title=musikktrykk)";
	} else {
		$query = "(fo=$q or eo=$q) not (ti=lydopptak or ti=video or ti=musikktrykk)";
	}

	echo(modulsearch($query, 'dvd'));
	
}

?>