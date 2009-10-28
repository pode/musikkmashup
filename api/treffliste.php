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
include_once('../include/functions.php');
require('File/MARCXML.php');


if (!empty($_GET['artist']) && !empty($_GET['bib'])) {

	$q = masser_input($_GET['artist']);
	$query = '';
	if (!empty($config['libraries'][$_GET['bib']]['sru'])) {
		$qu = urlencode($q);
		$query = '(dc.author=' . $qu . ')+and+dc.title=lydopptak';
	} else {
		$query = "(fo=$q or in=$q) and ti=lydopptak";
	}
	echo(podesearch($query));
	
}

?>