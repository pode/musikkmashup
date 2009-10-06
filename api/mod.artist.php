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
		echo("<p>Navn: {$art['name']}</p>");
		if ($art['image']['large']) {
			echo('<p class="artistbilde"><img src="' . $art['image']['large'] . '" alt="' . $art['name'] . '" title="' . $art['name'] . '" /></p>');
		}
		if ($art['bio']['summary']) {
			echo("<p>Biografi fra Last.fm: " . lastfm_lenker($art['bio']['summary']) . "</p>");
		}
		echo("<p>Lignende artister:</p><ul>");
		foreach ($art['similar'] as $sim) {
			echo('<li><a href="?q=' . urlencode($sim['name']) . '&bib=' . $_GET['bib'] . '">' . $sim['name'] . '</a></li>');
		}
		echo("</ul>");
		// Mer info
		echo('<p><a href="' . $art['url'] . '">Les mer hos Last.fm</a></p>');
	} else {
		// Error: show which error and go no further.
		echo '<b>Error '.$artist->error['code'].' - </b><i>'.$artist->error['desc'].'</i>';
	}
	
}

?>