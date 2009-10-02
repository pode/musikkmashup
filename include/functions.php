<?php 

/*
Funksjoner som benyttes av Pode musikkmashup
*/

function sru_search ($q, $bib) {
	
	global $config;
	require 'File/MARCXML.php';
	
	$version = '1.2';
	$recordSchema = 'marcxml';
	$startRecord = 1; 
	$maximumRecords = 5;
	
	$sru_url = $config['libraries'][$_GET['bib']]['sru'];
	$sru_url .= "?operation=searchRetrieve";
	$sru_url .= "&version=$version";
	$sru_url .= "&query=$q";
	$sru_url .= "&recordSchema=$recordSchema";
	$sru_url .= "&startRecord=$startRecord";
	$sru_url .= "&maximumRecords=$maximumRecords";
	
	$sru_data = file_get_contents($sru_url) or exit("Feil");
	$sru_data = str_replace("<record xmlns=\"http://www.loc.gov/MARC21/slim\">", "<record>", $sru_data);
	
	preg_match_all('/(<record>.*?<\/record>)/si', $sru_data, $treff);
	$marcxml = implode("\n\n", $treff[0]);
	$marcxml = '<?xml version="1.0" encoding="utf-8"?>' . "\n<collection>\n$marcxml\n</collection>";
	
	echo("\n\n <!-- \n\n $marcxml \n\n --> \n\n ");
	
	$out = "";
	
	// Retrieve a set of MARCXML records from a string
	$poster = new File_MARCXML($marcxml, File_MARC::SOURCE_STRING);

	// Iterate through the retrieved records
	while ($post = $poster->next()) {
	    // Pretty print each record
	    $out .= $post->getField("245")->getSubfield("a");
	}
	
	return $out;
	
}

?>