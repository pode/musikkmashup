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
		$query = "dc.author=$qu+and+dc.title=dvd";
	} else {
		$query = "fo=$q and ti=dvd";
	}

	echo(modulsearch($query, 'dvd'));
	
}

?>