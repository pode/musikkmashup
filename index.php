<?php echo('<?xml version="1.0" encoding="utf-8"?>'); ?>

<?php 

include('config.php');
include('include/functions.php');

echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'); 
echo('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nb_NO" lang="nb_NO">');
echo("<head>\n<title>" . $config['navn'] . "</title>\n");

echo('
<link rel="stylesheet" type="text/css" href="css/musikkmashup.css" />
');

echo("<body>");
echo("<h1>{$config['navn']}</h1>");

/* SØKESKJERM */

echo('
<div class="searchform">
<form method="get" action="">
<p>
<input type="text" size="50" name="q" value="' . $_GET['q'] . '" />
<select name="bib">
');
//skriver nedtrekksliste
foreach ($config['libraries'] as $key => $value)
{
	if ($selected==$key)
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
if ((!empty($_GET['q']) || !empty($_GET['item'])) && !empty($_GET['bib']) && !empty($config['libraries'][$_GET['bib']])) {

	echo('<div id="main">');
	
	/* TREFFLISTE */
	
	echo('<div id="left">');

	// Søk
	if (!empty($_GET['q'])) {
		if (!empty($config['libraries'][$_GET['bib']]['sru'])) {
			echo(sru_search($_GET['q'], $_GET['bib']));
		} else {
			echo("Z39.50 søk");	
		}
	}

	// Postvisning	
	if (!empty($_GET['item'])) {
		if (!empty($config['libraries'][$_GET['bib']]['sru'])) {
			echo("SRU postvisning!");
		} else {
			echo("Z39.50 postvisning");	
		}
	}


	echo('</div>');
	
	/* BOKSER MED EKSTRAINFO */
	
	echo('<div id="right">');
	echo("test");
	echo('</div>');
	
	// Avslutter div main
	echo('</div>');

}

echo("</body>\n</html>");

?>