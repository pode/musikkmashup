<?php

include('../config.php');
require('File/MARCXML.php');

if ($config['moduler'][$_GET['mod']]['aktiv']) {
	include('mod.' . $_GET['mod'] . '.php');
}

?>