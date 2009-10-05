<?php

include('../config.php');

if ($config['moduler'][$_GET['mod']]['aktiv']) {
	include('mod.' . $_GET['mod'] . '.php');
}

?>