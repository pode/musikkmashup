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