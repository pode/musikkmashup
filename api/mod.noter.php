<?php

include_once('../config.php');

if (!$config['moduler']['noter']['aktiv']) {
  exit;
}

include_once('../include/functions.php');

if (!empty($_GET['artist'])) {
	
	$q = masser_input($_GET['artist']);
	$query = '';
	if ($config['libraries'][$_GET['bib']]['sru']) {
		$qu = urlencode($q);
		$query = "dc.author=$qu+and+dc.title=musikktrykk";
	} else {
		$query = "(fo=$q or in=$q or eo=$q) and ti=musikktrykk";
	}

	echo(modulsearch($query, 'noter'));
	
}

?>