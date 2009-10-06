<?php 

/*
Funksjoner som benyttes av Pode musikkmashup
*/

function sru_search ($query, $bib, $limit=100, $postvisning=false) {
	
	global $config;

	// Variabel som skal samle opp output
	$out = "";
	
	$marcxml = get_sru($query, $bib, $limit);
	
	if ($config['debug']) {
		$out .= "<!-- Query: $query -->";
		$out .= "\n\n <!-- \n\n $marcxml \n\n --> \n\n ";
	}
	
	// Hent ut MARC-postene fra strengen i $marcxml
	$sru_poster = new File_MARCXML($marcxml, File_MARC::SOURCE_STRING);
	
	// Gå igjennom postene
	$antall_poster = 0;
	$poster = array();
	while ($post = $sru_poster->next()) {
		$poster[] = get_basisinfo($post, $bib, $postvisning);
		$antall_poster++;
	}
	
	// Sorter
	$poster = sorter($poster);
	
	// Sjekk om vi skal vise et utsnitt
	if ($antall_poster > $config['pr_side']) {
		
		// Plukker ut poster som skal vises
		$side = !empty($_GET['side']) ? $_GET['side'] : 1;
		$offset = ($side - 1) * $config['pr_side'];
		$lengde = $config['pr_side'];
		$poster = array_slice($poster, $offset, $lengde);
		
		// Lenker for blaing
		$forste = $offset + 1;
		$siste = $forste + $config['pr_side'] - 1;
		if ($siste > $antall_poster) {
			$siste = $antall_poster;
		}
		$out .= '<p id="blaing">' . "Viser treff $forste - $siste av $antall_poster. ";
		$blaurl = '?q=' . $_GET['q'] . '&bib=' . $_GET['bib'] . '&sorter=' . $_GET['sorter'] . '&orden=' . $_GET['orden'] . '&side=';
		if ($side > 1) {
			$forrigeside = $side - 1;
			$out .= '<a href="' . $blaurl . $forrigeside . '">Vis forrige side</a> ';
		} else {
			$out .= 'Vis forrige side ';
		}
		// (($page + 1) * $perPage) &gt; $hits + $perPage
		if ((($side + 1) * $config['pr_side']) > ($antall_poster + $config['pr_side'])) {
			$out .= 'Vis neste side ';
		} else {
			$nesteside = $side + 1;
			$out .= '<a href="' . $blaurl . $nesteside . '">Vis neste side</a> ';
		}
		$out .= '</p>';
	}

	// Legg til de sorterte postene i $out
	foreach ($poster as $post) {
		$out .= $post['post'];
	}
	
	if ($antall_poster == 0) {
		$out .= '<p>Beklager, null treff...</p>';	
	}
	
	return $out;
	
}

/*
Sorter postene. Dersom ikke både sorter og orden er satt bruker vi default sortering (år, synkende).
*/
function sorter($poster) {
	
	if ((!empty($_GET['sorter']) && 
			($_GET['sorter'] == 'aar' || 
			 $_GET['sorter'] == 'tittel' ||
			 $_GET['sorter'] == 'artist')
			 ) && 
		(!empty($_GET['orden']) && 
			($_GET['orden'] == 'stig' ||
			 $_GET['orden'] == 'synk')
			 )
		) {
			
		if ($_GET['sorter'] == 'aar' && $_GET['orden'] == 'synk') {
			usort($poster, "sorter_aar_synkende");
		} elseif ($_GET['sorter'] == 'aar' && $_GET['orden'] == 'stig') {
			usort($poster, "sorter_aar_stigende");
		} elseif ($_GET['sorter'] == 'tittel' && $_GET['orden'] == 'synk') {
			usort($poster, "sorter_tittel_synkende");
		} elseif ($_GET['sorter'] == 'tittel' && $_GET['orden'] == 'stig') {
			usort($poster, "sorter_tittel_stigende");
		} elseif ($_GET['sorter'] == 'artist' && $_GET['orden'] == 'synk') {
			usort($poster, "sorter_artist_synkende");
		} elseif ($_GET['sorter'] == 'artist' && $_GET['orden'] == 'stig') {
			usort($poster, "sorter_artist_stigende");
		} 
		
	} else {
		usort($poster, "sorter_aar_synkende");
	}
	
	return $poster;
	
}

function sorter_aar_synkende($a, $b) {
    return strcmp($b["aar"], $a["aar"]);
}

function sorter_aar_stigende($a, $b) {
    return strcmp($a["aar"], $b["aar"]);
}

function sorter_tittel_synkende($a, $b) {
    return strcmp($b["tittel"], $a["tittel"]);
}

function sorter_tittel_stigende($a, $b) {
    return strcmp($a["tittel"], $b["tittel"]);
}

function sorter_artist_synkende($a, $b) {
    return strcmp($b["artist"], $a["artist"]);
}

function sorter_artist_stigende($a, $b) {
    return strcmp($a["artist"], $b["artist"]);
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
		$data = get_basisinfo($post, $bib, true);
		$out .= $data['post'];
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

	// BYGG OPP ENKEL POSTVISNING

    $out = '<div class="basisinfo">';
    
    // Tittel
    if ($post->getField("245")->getSubfield("a")) {
    	// Sett sammen URL til posten i katalogen
    	$itemurl = '';
    	if ($config['libraries'][$bib]['item_url']) {
    		$itemurl = $config['libraries'][$bib]['item_url'] . $bibid;
    	}
    	// Fjern eventuelle punktum på slutten av tittelen
    	$tittel = preg_replace("/\.$/", "", marctrim($post->getField("245")->getSubfield("a")));
    	$out .= '<a href="' . $itemurl . '" class="albumtittel" title="Vis i katalogen til ' . $config['libraries'][$bib]['title'] . '">' . $tittel . '</a>';
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
    
    // HENT UT DATA FOR SORTERING
    
    $data = array();

    // Tittel
   	$data['tittel'] = marctrim($post->getField("245")->getSubfield("a")); 
    if ($post->getField("245") && $post->getField("245")->getSubfield("b")) {
    	$data['tittel'] .= " " . marctrim($post->getField("245")->getSubfield("b"));
    }
    
    // Artist
    if ($post->getField("100") && $post->getField("100")->getSubfield("a")) {
    	$data['artist'] = marctrim($post->getField("100")->getSubfield("a"));
    }
    if ($post->getField("110") && $post->getField("110")->getSubfield("a")) {
    	$data['artist'] = marctrim($post->getField("110")->getSubfield("a"));
    }
    
    // År
   	if ($post->getField("260")->getSubfield("c")) {
   		preg_match("/\d{4}/", marctrim($post->getField("260")->getSubfield("c")), $match);
   		$data['aar'] = $match[0];
   	}
   	
   	// Legg til post for visning
    $data['post'] = $out;

    return $data;
	
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