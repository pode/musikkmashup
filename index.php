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
<select name="bib">');
//skriver nedtrekksliste
foreach ($config['libraries'] as $key => $value)
{
	if ($selected==$key)
		echo('<option selected="selected" value="' . $key . '">' . $value['title'] . '</option>');
	else
		echo('<option value="$key">' . $value['title'] . '</option>');
}
echo('
</select>
<input type="submit" value="Søk" />
</p>
</form>
</div>');

/* TREFFLISTE */

echo('<div id="left">')
echo('</div>');

/* BOKSER MED EKSTRAINFO */

echo('<div id="right">')
echo('</div>');

echo("</body>\n</html>");

?>