<?php 

/* Konfigurasjonsfil for Podes musikkmashup */

// Navnet som vises i title-taggen og øverst på alle sider
$config['navn'] = 'Podes musikkmashup';

/*
BIBLIOTEK

Her konfigureres de bibliotekene det skal være mulig å søke i, og 
de opplysningene som trengs for å utføre søket. Rekkefølgen her
bestemmer rekkefølgen når bibliotekene skal velges ved søk. 

Opplysninger som trengs: 
title: navn på biblioteket 
z3950 ELLER sru og item_url
z3950: tilkoblings-streng for Z39.50
sru: tilkoblingsstreng for SRU
item_url: grunn-URL for postvisning i katalogen
*/

$config['libraries']['pode'] = array(
	'title'    => 'Pode', 
	'sru'      => 'http://torfeus.deich.folkebibl.no:9999/biblios', 
	'item_url' => 'http://dev.bibpode.no/cgi-bin/koha/opac-detail.pl?biblionumber='
);
$config['libraries']['deich'] = array(
	'title' => 'Deichmanske', 
	'z3950' => 'z3950.deich.folkebibl.no:210/data'
);

/*

Flere bibliotek, for eventuell testing

$config['libraries']['bibsys'] = array(
	'title' => 'BIBSYS', 
	'z3950' => 'z3950.bibsys.no:2100/BIBSYS', 
);
$config['libraries']['trondheim'] = array(
	'title' => 'Trondheim folkebibliotek', 
	'z3950' => 'z3950.trondheim.folkebibl.no:210/data', 
);
$config['libraries']['bergen'] = array(
	'title' => 'Bergen offentlige', 
	'z3950' => 'z3950.bergen.folkebibl.no:210/data', 
);

*/

/* 
LAST.FM 

Informasjon om APIet: 
http://www.last.fm/api

For testing kan du bruke API-nøkkelen vår som er oppgitt nedenfor, skal du 
ta denne applikasjonen i bruk må du skaffe deg din egen, og bytte den ut med 
vår nedenfor. Se her for mer info: 
http://www.last.fm/api/account

*/

$config['lastfm'] = array(
	'api_key' => 'b1e5430e679a81219cd8ea2d0e624aaa', 
	'api_root' => 'http://ws.audioscrobbler.com/2.0/'
);

?>