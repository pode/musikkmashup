<?php 

/*
Funksjoner som benyttes av Pode musikkmashup
*/

function sru_search ($query, $bib, $limit=25, $postvisning=false) {
	
	global $config;

	// Variabel som skal samle opp output
	$out = "";
	
	$marcxml = get_sru($query, $bib, $limit);
	
	if ($config['debug']) {
		$out .= "<!-- Query: $query -->";
		$out .= "\n\n <!-- \n\n $marcxml \n\n --> \n\n ";
	}
	
	// Hent ut MARC-postene fra strengen i $marcxml
	$poster = new File_MARCXML($marcxml, File_MARC::SOURCE_STRING);

	// Gå igjennom postene
	$antall_poster = 0;
	while ($post = $poster->next()) {
		$out .= get_basisinfo($post, $bib, $postvisning);
		$antall_poster++;
	}
	if ($antall_poster == 0) {
		$out .= '<p>Beklager, null treff...</p>';	
	}
	
	return $out;
	
}

function sru_postvisning($id, $bib) {

	global $config;
	
	$marcxml = get_sru('rec.id=' . urlencode($id), $bib, 1);
	
	if ($config['debug']) {
		echo("\n\n <!-- \n\n $marcxml \n\n --> \n\n ");
	}
	
	// Variabel som skal samle opp output
	$out = "";
	
	// Hent ut MARC-postene fra strengen i $marcxml
	$poster = new File_MARCXML($marcxml, File_MARC::SOURCE_STRING);

	// Gå igjennom postene
	while ($post = $poster->next()) {
		$out .= get_basisinfo($post, $bib, true);
		$out .= get_detaljer($post, $bib);
	}
	
	return $out;
	
}

function get_sru($query, $bib, $limit) {
	
	global $config;
	
	$version = '1.2';
	$recordSchema = 'marcxml';
	$startRecord = 1; 
	$maximumRecords = $limit;
	
	// Bygg opp SRU-urlen
	$sru_url = $config['libraries'][$bib]['sru'];
	$sru_url .= "?operation=searchRetrieve";
	$sru_url .= "&version=$version";
	$sru_url .= "&query=$query";
	$sru_url .= "&recordSchema=$recordSchema";
	$sru_url .= "&startRecord=$startRecord";
	$sru_url .= "&maximumRecords=$maximumRecords";
	
	// Hent SRU-data
	$sru_data = file_get_contents($sru_url) or exit("Feil");
	
	// Massér SRU-dataene slik at vi lett kan nehandle dem med funksjonene fra File_MARC
	$sru_data = str_replace("<record xmlns=\"http://www.loc.gov/MARC21/slim\">", "<record>", $sru_data);
	preg_match_all('/(<record>.*?<\/record>)/si', $sru_data, $treff);
	$marcxml = implode("\n\n", $treff[0]);
	$marcxml = '<?xml version="1.0" encoding="utf-8"?>' . "\n<collection>\n$marcxml\n</collection>";
	
	return $marcxml;

}

function get_basisinfo($post, $bib, $postvisning) {

	global $config;

	$bibid = marctrim($post->getField("999")->getSubfield("c"));

    $out = '<div class="basisinfo">';
    
    // Tittel
    if ($post->getField("245")->getSubfield("a")) {
    	// Sett sammen URL til posten i katalogen
    	$itemurl = '';
    	if ($config['libraries'][$bib]['item_url']) {
    		$itemurl = $config['libraries'][$bib]['item_url'] . $bibid;
    	}
    	$out .= '<a href="' . $itemurl . '" class="albumtittel" title="Vis i katalogen til ' . $config['libraries'][$bib]['title'] . '">' . marctrim($post->getField("245")->getSubfield("a")) . '</a>';
    }
    if ($post->getField("245") && $post->getField("245")->getSubfield("b")) {
    	$out .= ' : ' . marctrim($post->getField("245")->getSubfield("b"));
    }
    
    // Artist
    if ($post->getField("100") && $post->getField("100")->getSubfield("a")) {
    	$out .= '<br />';
    	$out .= '<span class="artist">' . marctrim($post->getField("100")->getSubfield("a")) . '</span>';
    	if ($post->getField("100")->getSubfield("q")) {
    		$out .= ' (' . marctrim($post->getField("100")->getSubfield("q")) . ')';
    	}
    }
    if ($post->getField("110") && $post->getField("110")->getSubfield("a")) {
    	$out .= '<br />';
    	$out .= '<span class="artist">' . marctrim($post->getField("110")->getSubfield("a")) . '</span>';
    	if ($post->getField("110")->getSubfield("q")) {
    		$out .= ' (' . marctrim($post->getField("110")->getSubfield("q")) . ')';
    	}
    }
    if (!$post->getField("100") && !$post->getField("110")) {
    	if ($post->getField("511") && $post->getField("511")->getSubfield("a")) {
    		$out .= '<br />';
    		$out .= marctrim($post->getField("511")->getSubfield("a"));
    	}
    }
    $out .= '<br />';
    
    // Sted, utgiver, år
    if ($post->getField("260")) {
    	if ($post->getField("260")->getSubfield("a")) {
    		if (marctrim($post->getField("260")->getSubfield("a")) != '[S.l.]') {
    			$out .= marctrim($post->getField("260")->getSubfield("a")) . ', ';
    		}
    	}
    	if ($post->getField("260")->getSubfield("b")) {
    		$out .= marctrim($post->getField("260")->getSubfield("b")) . ', ';
    	}
    	if ($post->getField("260")->getSubfield("c")) {
    		$out .= marctrim($post->getField("260")->getSubfield("c"));
    	}
    }
    if (!$postvisning) {
	    $out .= ' [<a href="?bib=' . $bib . '&id=' . $bibid . '">Vis detaljer</a>]';
    }
    $out .= '</div>';
    
    return $out;
	
}

function get_detaljer($post, $bib) {

	$out = '<div class="detaljer">';
	
	// INNHOLD
	
	// Hent ut spor-navn fra 740$2, indikator 2 = 2 (analytt)
	if ($post->getField("740")) {
		$out .= '<p>Spor:</p>';
		$out .= '<ul>';
		$fields740 = $post->getFields("740");
		foreach ($fields740 as $field740) {
			// Sjekk om dette er en analytt
			if ($field740->getIndicator(2) == 2) {
				$tittel = marctrim($field740->getSubfield("a"));
				$tittelu = urlencode($tittel);
	    		$out .= '<li><a href="?q=' . $tittelu . '&bib=' . $bib . '">' . $tittel . '</a></li>';
			}
	    }
	    $out .= '</ul>';
	// Eller hent info fra 505
	} else {
		if ($post->getField("505") && $post->getField("505")->getSubfield("a")) {
    		$out .= '<p>' . marctrim($post->getField("505")->getSubfield("a")) . '</p>';
    	}	
	}
	
	// MEDVIRKENDE
	
	if ($post->getField("700") && $post->getField("700")->getSubfield("a")) {
		$out .= '<p>Medvirkende:</p><ul>';
		foreach ($post->getFields("700") as $med) {
			$med = marctrim($med->getSubfield("a"));
			$out .= '<li><a href="?q=' . urlencode($med) . '&bib=' . $bib . '">' . $med . '</a></li>';
		}
		$out .= '</ul>';
	}
	
	// EMNER
	
	$emner = $post->getFields('6\d\d', true);
	if ($emner) {
		$out .= '<p>Emner:</p>';
		$out .= '<ul>';
		foreach ($emner as $emne) {
	   		$out .= '<li>' . marctrim($emne->getSubfield("a")) . '</li>';
	    }
	}
    $out .= '</ul>';
	
	$out .= '</div>';
	
	return $out;
	
}

function masser_input($s) {

	// Fjern komma fra feks Asbjørnsen, Kristin
	$s = str_replace(',', '', $s);
	// Fjern &
	$s = str_replace('&', '', $s);
	
	return $s;
	
}

/*
Teksten som kommer fra LastFM fører av en eller annen grunn ut i intet. 
Denne funksjonen fjerner foreløpig lenkene, etter hvert vil den endre dem så de peker til 
rett sted. 
*/
function lastfm_lenker($s) {

	$s = preg_replace("/<a .*?>(.*?)<\/a>/i", "$1", $s);
	return $s;
	
}

/*
Last.fm foretrkker Susanne Lundeng fremfor Lundeng, Susanne
*/
function avinverter($s) {
	// Sjekk om strengen inneholder noe komma
	if (substr_count($s, ',', 2) > 0) {
		list($first, $last) = split(', ', $s);
		return "$last $first";
	} else {
		return $s;
	}
}

/*
Av en eller annen grunn gir dette: 
$post->getField("zzz")->getSubfield("a")
alltid dette: 
[a]: Tittelen kommer her...
Denne funksjonen kapper av de 5 første tegnene, slik at vi får ut den faktiske tittelen
*/

function marctrim($s) {
	
	return substr($s, 5);
	
}

?>