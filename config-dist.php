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

/* Konfigurasjonsfil for Podes musikkmashup */

// Navnet som vises i title-taggen og øverst på alle sider
$config['navn'] = 'Podes musikkmashup';

// Ledetekst for søkeboksen
$config['ledetekst'] = 'Artist/album: ';

// Skal vi skrive ut litt ekstra debug-info? 
// Verdier: true eller false
$config['debug'] = true;

// Hvor mange poster skal maksimalt hentes? 
$config['maks_poster'] = 10000;
// Hvor mange poster skal vises pr side i hovedvisningen? 
$config['pr_side'] = 10000;

// Skal det vises lenke til katalogen på postene? 
$config['vis_kataloglenke'] = false;
// Skal brukeren få mulighet til å velge sortering? 
$config['vis_sortering'] = false;

/*
GOOGLE ANALYTICS

Du kan bruke Google analytics for å se statistikk for bruken av denne applikasjonen. 
Du trenger en kode for å aktivisere denne tjenesten, dette jkan du få her: 
https://www.google.com/analytics/
Eksempel: $config['google_analytics'] = "UA-12345678-1";
Dersom du ikke oppgir noen kode nedenfor vil Google Analytics ikke bli aktivert. 
*/

$config['google_analytics'] = "";

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
	'api_key' => 'Lim inn din API-nøkkel her', 
	'api_root' => 'http://ws.audioscrobbler.com/2.0/'
);

/*
MODULER

Moduler konfigureres med et array på formen
$config['moduler']['MODUL'] = array();
der MODUL tilsvarer den midterste delen av filnavnet modulen 
er implementert i: mod.MODUL.php. 

Rekkefølgen på modulene nedenfor bestemmer rekkfølgen modulene
vises i på siden. (Men dette kan overstyres av brukerne, som selv kan
flytte rundt på modulene.)

Alle moduler har minst to parametere: 
'aktiv': true eller false, dvs om modulen er slått av eller på. 
'tittel': tittelen som vises i modul/widget-boksen

Dersom modulen inneholder en liste med elementer hvor antallet 
elementer skal kunne begrenses ved hjelp av en parameter gjøres 
dette med en parameter som heter 'antall'.
*/

$config['moduler']['albuminfo'] = array(
  'aktiv' => true, 
  'tittel' => 'Albuminfo', 
  'undertittel' => 'Hentet fra Last.fm'
);

$config['moduler']['artist'] = array(
  'aktiv' => true, 
  'tittel' => "Artistinfo", 
  'undertittel' => 'Hentet fra Last.fm', 
  // Skal artister med null treff vises i lista over "Lignende artister"? 
  'vis_med_null_treff' => true
);

$config['moduler']['dvd'] = array(
  'aktiv' => true, 
  'tittel' => "DVDer",
  'undertittel' => 'Hentet fra bibliotekkatalogen',  
  'antall' => 10
);

$config['moduler']['noter'] = array(
  'aktiv' => true, 
  'tittel' => "Noter", 
  'undertittel' => 'Hentet fra bibliotekkatalogen',
  'antall' => 10
);

$config['moduler']['bok'] = array(
  'aktiv' => true, 
  'tittel' => "Bøker m.m.", 
  'undertittel' => 'Hentet fra bibliotekkatalogen',
  'antall' => 10
);

?>
