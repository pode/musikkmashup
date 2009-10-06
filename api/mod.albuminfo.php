<?php

include_once('../config.php');

if (!$config['moduler']['albuminfo']['aktiv']) {
  exit;
}

include_once('../include/functions.php');

if (!empty($_GET['album']) && !empty($_GET['artist'])) {
	
	$url = $config['lastfm']['api_root'];
	$url .= "?method=album.getinfo";
	$url .= "&api_key=" . $config['lastfm']['api_key'];
	$url .= "&artist=" . urlencode(avinverter($_GET['artist']));
	$url .= "&album=" . urlencode($_GET['album']);
	$url .= "&format=json";
	
	if ($album = json_decode(file_get_contents($url), true)) {
		// Sjekk om det er noe feil
		if ($album['error']) {
			echo("<p>Beklager, det oppstod en feil!<br />({$album['message']})</p>");
			exit;
		}
		// Tittel
		echo("<p>{$album['album']['name']}</p>");
		// Bilde
		if ($album['album']['image'][2]['#text']) {
			echo('<p class="albumbilde"><img src="' . $album['album']['image'][2]['#text'] . '" alt="' . $album['album']['name'] . '" title="' . $album['album']['name'] . '" /></p>');
		}
		// Beskrivelse
		if ($album['album']['wiki']['summary']) {
			echo("<p>Beskrivelse fra Last.fm: {$album['album']['wiki']['summary']}</p>");
		}
	
	}
	
}

?>