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
		
		// Gjør widgetene synlige
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