<?php

/*

Copyright 2009 ABM-utvikling

This file is part of "Podes musikkmashup".

"Podes musikkmashup" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

"Podes musikkmashup" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with "Podes musikkmashup".  If not, see <http://www.gnu.org/licenses/>.

Source code available from: 
http://github.com/pode/musikkmashup/

*/

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
		echo('<p class="overskrift">' . $album['album']['name'] . '</p>');
		// Bilde
		if ($album['album']['image'][2]['#text']) {
			echo('<p class="albumbilde"><img src="' . $album['album']['image'][2]['#text'] . '" alt="' . $album['album']['name'] . '" title="' . $album['album']['name'] . '" /></p>');
		}
		// Beskrivelse
		if ($album['album']['wiki']['summary']) {
			echo('<p>' . lastfm_lenker($album['album']['wiki']['summary'])  . '</p>');
		}
		// Mer info
		echo('<p class="les-mer"><a href="' . $album['album']['url'] . '">Les mer hos Last.fm</a></p>');
	}
	
}

?>