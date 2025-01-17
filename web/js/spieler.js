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
        var $input = $(this); // Das aktuelle Input-Feld
        var data = $input.data('vereine'); // JSON-Daten aus data-vereine holen
        
        // Autocomplete-Funktion initialisieren
        $input.autocomplete({
            source: data.map(item => ({
                label: item.klarname,
                value: item.label, // Zeige den Namen des Vereins im Input-Feld
                id: item.value,    // Speichere die ID
            })),
            select: function (event, ui) {
                var index = $input.attr('id').split('-').pop(); // Extrahiere den Index aus der ID
                var hiddenField = $("#hidden-verein-id-" + index); // Hidden-Feld für den Verein
                hiddenField.val(ui.item.id); // Setze die Verein-ID in das Hidden-Feld
                
                // Optional: Debugging
                console.log("Selected Verein ID: " + ui.item.id);
                console.log("Hidden Field Updated: " + hiddenField.attr('id'));
            },
        });
    });
});