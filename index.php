<?php 
header('Content-Type: text/html; charset=utf-8');
echo('<?xml version="1.0" encoding="utf-8"?>'); 

include('config.php');
include('include/functions.php');
require('File/MARCXML.php');

echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'); 
echo('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nb_NO" lang="nb_NO">');
echo("<head>\n<title>{$config['navn']}</title>\n");

echo('
<link rel="stylesheet" type="text/css" href="css/musikkmashup.css" />
<script src="http://www.google.com/jsapi"></script>
<script>
  // Hent inn jQuery fra Googles JavaScript API
  google.load("jquery", "1.3.2");
  google.load("jqueryui", "1.7.2");
</script>
<script src="scripts/jquery.easywidgets.min.js" type="text/javascript"></script>
<script src="scripts/musikk.js" type="text/javascript"></script>
');

echo('<body>');
echo('<div id="content">');
echo("<h1>{$config['navn']}</h1>");

/* SØKESKJERM */

echo('
<div class="searchform">
<form method="get" action="">
<p>');
echo($config['ledetekst']);
echo('<input type="text" size="50" name="q" value="' . $_GET['q'] . '" />
<select name="bib">
');
//skriver nedtrekksliste
foreach ($config['libraries'] as $key => $value)
{
	if ($_GET['bib'] == $key)
		echo('<option selected="selected" value="' . $key . '">' . $value['title'] . '</option>' . "\n");
	else
		echo('<option value="' . $key . '">' . $value['title'] . '</option>' . "\n");
}
echo('
</select>
<input type="submit" value="Søk" />
</p>
</form>
</div>');

// q eller item må være satt
// bib må være satt, og må være en nøkkel i $config['libraries']
if ((!empty($_GET['q']) || !empty($_GET['id'])) && !empty($_GET['bib']) && !empty($config['libraries'][$_GET['bib']])) {

	echo('<div id="main">');
	
	/* TREFFLISTE */
	
	echo('<div id="left" class="widget">');
	echo('<div class="widget-header">Musikk-CD</div>');
	
	// Sortering
	if (!empty($_GET['q'])) {
		if ($config['vis_sortering']) {
			echo('<div id="sortering"><form>');
			echo('Sorter på ');
			echo('<input type="hidden" name="q" value="' . $_GET['q'] . '" />');
			echo('<input type="hidden" name="bib" value="' . $_GET['bib'] . '" />');
			echo('<input type="hidden" name="side" value="' . $_GET['side'] . '" />');
			echo('<select name="sorter">');
			$sorter = array('aar'=>'utgivelsesår', 'tittel'=>'tittel', 'artist'=>'artist');
			foreach ($sorter as $verdi => $tekst) {
				echo('<option value="' . $verdi . '"');
				if (!empty($_GET['sorter']) && $_GET['sorter'] == $verdi) {
					echo(' selected="selected"');	
				}
				echo('>' . $tekst . '</option>');
			}
			echo('</select>');
			echo('<select name="orden">');
			$orden = array('synk'=>'synkende', 'stig'=>'stigende');
			foreach ($orden as $verdi => $tekst) {
				echo('<option value="' . $verdi . '"');
				if (!empty($_GET['orden']) && $_GET['orden'] == $verdi) {
					echo(' selected="selected"');	
				}
				echo('>' . $tekst . '</option>');
			}
			echo('</select>');
			echo('<input type="submit" value="Sorter" />');
			echo("</form></div>");
		}
	
		// Søk
		if (!empty($_GET['q'])) {
			$q = masser_input($_GET['q']);
			$query = '';
			if (!empty($config['libraries'][$_GET['bib']]['sru'])) {
				$qu = urlencode($q);
				$query = '(dc.author=' . $qu . '+or+dc.title=' . $qu . ')+and+dc.title=lydopptak';
			} else {
				$query = "(fo=$q or in=$q or ti=$q) and ti=lydopptak";
			}
			echo(podesearch($query));
		}
	}

	// Postvisning	
	if (!empty($_GET['id'])) {
		echo(postvisning($_GET['id']));
	}

	echo('</div>');
	
	/* BOKSER MED EKSTRAINFO */
	
	echo('<div id="right">');

	echo('<div id="artistvelger"><form><select name="q" id="artistvalg" onChange="setArtist(this.options[this.selectedIndex].value)"><option>Velg artist...</option></select></form></div>');

	foreach ($config['moduler'] as $key => $mod) {
		if ($mod['aktiv']) {
			echo('<div class="widget movable collapsable right-col-box" id="widget_' . $key . '">');
			echo('	<div class="widget-header">' . $mod['tittel']);
			if ($mod['undertittel']) {
				echo(' <span class="undertittel">' . $mod['undertittel'] . '</span>');
			}
			echo('</div>');
			echo('  <div class="widget-content"><img src="images/widgets/loading.gif" alt="Henter data..." /></div>');
			echo('</div>');
		}				
	}

	echo('</div>');
	
	// Avslutter div main
	echo('</div>');

}

// Avslutter div content
echo('</div>');
echo("</body>\n</html>");

?>