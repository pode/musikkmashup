<?php

include_once('../config.php');

if (!$config['moduler']['artist']['aktiv']) {
  exit;
}

include_once('../include/functions.php');
include_once('../lastfmapi/lastfmapi.php');

if (!empty($_GET['artist'])) {
	
	$authVars['apiKey'] = $config['lastfm']['api_key'];
	$auth = new lastfmApiAuth('setsession', $authVars);
	$apiClass = new lastfmApi();
	$artist = $apiClass->getPackage($auth, 'artist');
	
	$methodVars = array(
		'artist' => avinverter($_GET['artist'])
	);
	if ( $art = $artist->getinfo($methodVars) ) {
		echo('<p class="overskrift">' . $art['name'] . '</p>');
		// Bilde
		if ($art['image']['large']) {
			echo('<p class="artistbilde"><img src="' . $art['image']['large'] . '" alt="' . $art['name'] . '" title="' . $art['name'] . '" /></p>');
		}
		// Biografi
		if ($art['bio']['summary']) {
			echo('<p>' . lastfm_lenker($art['bio']['summary']) . '</p>');
		}
		// Lignende artister
		echo('<p class="lignende-artister">Lignende artister:</p><ul>');
		foreach ($art['similar'] as $sim) {
			$antall_treff = antall_treff($sim['name']);
			if ($antall_treff > 0) {
				echo('<li><a href="?q=' . urlencode($sim['name']) . '&bib=' . $_GET['bib'] . '" class="artist-navn">' . $sim['name'] . '</a>');
				echo(' (' . $antall_treff . ')</li>');
			} elseif ($config['moduler']['artist']['vis_med_null_treff'])  {
				echo('<li>' . $sim['name'] . ' (' . $antall_treff . ')</li>');	
			}
		}
		echo("</ul>");
		// Mer info
		echo('<p class="les-mer"><a href="' . $art['url'] . '">Les mer hos Last.fm</a></p>');
	} else {
		// Error: show which error and go no further.
		echo '<b>Error '.$artist->error['code'].' - </b><i>'.$artist->error['desc'].'</i>';
	}
	
}

function antall_treff($q) {

	global $config;
	$bib = $_GET['bib'];
	$debug = 0;
	
	if ($config['libraries'][$bib]['sru']) {

		// SRU
		
		$qu = urlencode($q);
		$query = '(dc.author=' . $qu . '+or+dc.title=' . $qu . ')+and+dc.title=lydopptak';
		$sruurl = $config['libraries'][$bib]['sru'] . '?version=1.1&operation=searchRetrieve&query=' . $query;
		if ($xml = file_get_contents($sruurl)) {
			preg_match("/numberOfRecords>(.*?)</", $xml, $matches);
			return($matches[1]);
		} else {
			echo('error');	
		}

	} else {

		//Z39.50

		$ccl = "(fo=$q or in=$q or ti=$q) and ti=lydopptak";

		$host = $config['libraries'][$bib]['z3950'];
		$zconfig = get_zconfig();
		$type = 'xml';
		$syntax = 'NORMARC';
			
		$id = yaz_connect($host);
		yaz_element($id, "F");
		yaz_syntax($id, $syntax);
		yaz_range($id, 1, 1);
		
		yaz_ccl_conf($id, $zconfig);
		$cclresult = array();
		if (!yaz_ccl_parse($id, $ccl, $cclresult)) {
			echo($debug ? 'Error: '.$cclresult["errorstring"] : 'error');
		} else {
			$rpn = $cclresult["rpn"];
			yaz_search($id, "rpn", utf8_decode($rpn));
		}
		
		yaz_wait();
	
		$error = yaz_error($id);
		if (!empty($error))	{
			echo($debug ? "Error yazCclArray: $error" : 'error');
		} else {
			return(yaz_hits($id));
		}
	}
	
}


?>