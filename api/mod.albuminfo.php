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
	
	if ($album = simplexml_load_file($url)) {
		echo("<p>{$album->album->name}</p>");
		
		// echo($url);
		// print_r($album->album);
		
		if ($album->album->image[2]) {
			echo('<p class="albumbilde"><img src="' . $album->album->image[2] . '" alt="' . $album->album->name . '" title="' . $album->album->name . '" /></p>');
		}
		if ($alb['wiki']['summary']) {
			echo("<p>Beskrivelse fra Last.fm: {$alb['wiki']['summary']}</p>");
		}
	}
	
}

?>