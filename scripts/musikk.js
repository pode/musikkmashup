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

// Globale variabler
var antallArtister = 0;

// Dette er en jQuery funksjon som automatisk kjøres når hele siden er lastet inn 
// (dvs, egentlig når DOMen er klar)
$(function(){

	// Gjør boksene til "widgets"
	// useCookies : true gjør at det brukes cookies for å huske tilstanden til boksene 
	// mellom sesjoner
	$.fn.EasyWidgets({
	
		behaviour : {
			useCookies : true
		},
		
		i18n : {
			editText       : ' <img src="images/widgets/edit.png"     alt="Rediger" width="16" height="16" />',
			closeText      : ' <img src="images/widgets/close.png"    alt="Close"   width="16" height="16" />',
			collapseText   : ' <img src="images/widgets/collapse.png" alt="Lukk"    width="16" height="16" />',
			cancelEditText : ' <img src="images/widgets/edit.png"     alt="Avbryt"  width="16" height="16" />',
			extendText     : ' <img src="images/widgets/extend.png"   alt="Close"   width="16" height="16" />'
		}
	});

	// Bygger opp et array med navn på artistene som forekommer i lista
	var artister = new Array();
	jQuery.each($(".artist"), function() {
		var artist = $(this).text();
		if (jQuery.inArray(artist, artister) == -1) {
			artister.push(artist);
			// $("#right").append("<p>" + artist + "</p>");
		}
	});
	
	// debug 
	// alert(artister);
	
	//Sjekker hvor mange artister vi har funnet
	antallArtister = artister.length;
	if (antallArtister == 0) {
	
		// 0 artister
		$("#right").append("<p>Beklager, fant ingen artister...</p>");
	
	} else if (antallArtister == 1) {
	
		setArtist(artister[0]);
	
	} else {
	
		// Flere artister
		$("#artistvelger").css({'visibility' : 'visible'});
		jQuery.each(artister, function(i, n) {
			$("#artistvalg").append("<option>" + n + "</option>");
		});
	
	}
	
});

function setArtist(artist) {

		// Lag et array med albumtitler
		var albumer = new Array();
		jQuery.each($(".albumtittel"), function() {
			var album = $(this).text();
			if (jQuery.inArray(album, albumer) == -1) {
				albumer.push(album);
			}
		});
		// Fjern boksen for album-info?
		if (albumer.length > 1) {
			$("#widget_albuminfo").remove();
		}
		
		// Vis alle album før vi eventuelt skjuler noen
		var totalt_antall_album = 0;
		jQuery.each($(".basisinfo"), function() {
			var this_album = this;
			$(this_album).show();
			totalt_antall_album = totalt_antall_album + 1;
		});
		$(".antall-poster").text("Viser " + totalt_antall_album + " av " + totalt_antall_album + " treff");
		
		// Skjul irrelevante album i trefflista, hvis ikke artist = "_alle"
		if (artist != '_alle') {
			var antall_album = 0;
			var antall_skjulte_album = 0;
			jQuery.each($(".basisinfo"), function() {
				var this_album = this;
				var this_artist = $(this_album).find(".artist").text();
				if (this_artist != artist) {
					$(this_album).hide();
					antall_skjulte_album = antall_skjulte_album + 1;
				}
				antall_album = antall_album + 1;
			});
			var antall_viste_album = antall_album - antall_skjulte_album;
			// Sett antallet album
			$(".antall-poster").text("Viser " + antall_viste_album + " av " + antall_viste_album + " treff");
		}
				
		// Gjør widgetene synlige, hvis ikke artist = "_alle"
		if (artist != '_alle') {
			$(".widget").css({'visibility' : 'visible'});
			// Gjem artistvelgeren, hvis vi bare har en artist
			if (antallArtister == 1) {
				$("#artistvelger").remove();
			}
			// Gå igjennom alle widgetene og legg til innhold
			jQuery.each($(".widget"), function() {
				var this_widget = this;
				target = this_widget.id.replace(/widget_/g, "");
				
				// Vi sender den samme informasjonen til modulene, uavhengig av hva de skal gjøre for noe. 
				// På denne måten slipper vi å vite noe om hver enkelt modul før vi kaller dem opp. 
				$.get("api/index.php", { mod: target, 
										 artist: artist, 
										 album: albumer[0], 
				                         q: getQueryVariable('q'),
				                         bib: getQueryVariable('bib')
				                       },
				    function(data){
				      $("#" + this_widget.id).find(".widget-content").text("");
				      $("#" + this_widget.id).find(".widget-content").append(data);
				    });
			});
		} else {
			$(".widget").css({'visibility' : 'hidden'});
			$("#left").css({'visibility' : 'visible'});
		}
}

// From: http://www.webdeveloper.com/forum/showthread.php?t=166692
function getQueryVariable(variable)
{
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0; i<vars.length; i++)
	{
		pair = vars[i].split("=");
		if (pair[0] == variable)
		{
			str_arr = pair[1].split("+");
			return str_arr.join(" ");
		}
	}
	return "";
}