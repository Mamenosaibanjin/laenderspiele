// Event Listener für allgemeine Interaktionen
document.addEventListener("DOMContentLoaded", () => {
    console.log("spieler.js geladen - Funktionalität auf ActiveForm umgestellt");
});

// Funktion zur Initialisierung von Event-Listenern für Zeilen
function initializeRowEventListeners(row) {
    // Hier könnten spezifische Listener implementiert werden, falls erforderlich
    console.log("Event-Listener für neue Zeile initialisiert:", row);
}

$(document).ready(function () {
    // Initialisiere Autocomplete für alle Felder mit "autocomplete-verein-"
    $('input[id^="autocomplete-verein-"]').each(function () {
        var $input = $(this);
        var data = $input.data('vereine');
        
        $input.autocomplete({
            source: data.map(item => ({
                label: item.klarname,
                value: item.label,
                id: item.value,
            })),
            select: function (event, ui) {
                var index = $input.attr('id').split('-').pop();
                var hiddenField = $("#hidden-verein-id-" + index);
                hiddenField.val(ui.item.id);
                console.log("Selected Verein ID: " + ui.item.id);
                console.log("Hidden Field Updated: " + hiddenField.attr('id'));
            },
        });
    });

    // Initialisiere Autocomplete für alle Felder mit "autocomplete-land-"
    $('input[id^="autocomplete-land-"]').each(function () {
        var $input = $(this);
        var data = $input.data('nationen');
        
        $input.autocomplete({
            source: data.map(item => ({
                label: item.klarname,  
                value: item.label,     
                id: item.value,        
            })),
            select: function (event, ui) {
                var index = $input.attr('id').split('-').pop();
                var hiddenField = $("#hidden-land-id-" + index);
                hiddenField.val(ui.item.id);
                console.log("Selected Land ID: " + ui.item.id);
                console.log("Hidden Field Updated: " + hiddenField.attr('id'));
            },
        });
    });
	
	$('input[id^="autocomplete-wettbewerb-"]').each(function () {
	    var $input = $(this);
	    var data = $input.data('tournaments'); // Holt die Daten aus dem data-Attribut

	    if (!data) {
	        console.warn("Keine Turnierdaten für", $input.attr('id'));
	        return;
	    }

	    console.log("Lade Turnierdaten für", $input.attr('id'), data); // Debugging

	    $input.autocomplete({
	        source: data.map(item => ({
	            label: item.klarname,  // Angezeigter Text mit Jahr & Land
	            value: item.label,     // Der sichtbare Wert im Input
	            id: item.value,        // Die ID des Turniers
	        })),
	        select: function (event, ui) {
	            var index = $input.attr('id').split('-').pop();
	            var hiddenField = $("#hidden-tournament-id-" + index);
				hiddenField.val(ui.item.id);
	            console.log("Selected Tournament ID: " + ui.item.id);
	            console.log("Hidden Field Updated: " + hiddenField.attr('id'));
	        },
	    });
	});
});
