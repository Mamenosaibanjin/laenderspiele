$(document).ready(function () {
    // Initialisiere vorhandene Farb-Inputs
    $('.farbe-input').each(function () {
        initializeColorPicker($(this));
    });

    // Funktion zur Initialisierung eines Colorpickers
    function initializeColorPicker($input) {
        $input.spectrum({
            color: $input.val(),
            showInput: true,
            preferredFormat: "hex",
            change: function (color) {
                $input.val(color.toHexString()); // Speichert die gewählte Farbe im Input
            }
        });
    }

    // Hinzufügen eines neuen Farbfelds
    $('#add-color').on('click', function () {
        let $newInput = $('<input>', {
            type: 'text',
            name: 'farben[]',
            class: 'form-control farbe-input',
            value: '#ffffff' // Standardwert
        });

        $('#farben-container').append($newInput);
        initializeColorPicker($newInput);
    });

    // Entfernen eines Farbfelds
    $('#farben-container').on('dblclick', '.farbe-input', function () {
        if (confirm('Möchten Sie diese Farbe entfernen?')) {
            $(this).spectrum('destroy'); // Zerstört den Colorpicker
            $(this).remove();           // Entfernt das Input-Feld
        }
    });
});


// Autocomplete für Stadionfeld
$(document).ready(function () {
    const availableStadien = JSON.parse($('#autocomplete-stadion').attr('data-stadien'));

    $('#autocomplete-stadion').autocomplete({
        source: availableStadien,
        select: function (event, ui) {
            $('#autocomplete-stadion').val(ui.item.klarname);
            $('#hidden-stadion-id').val(ui.item.value);
            return false;
        }
    });

    $('#autocomplete-stadion').blur(function () {
        if ($('#hidden-stadion-id').val() === '') {
            $('#autocomplete-stadion').val('');
        }
    });
});

function updateStadiumList() {
    const stadionInput = document.getElementById('autocomplete-stadion');
    const updateUrl = stadionInput.dataset.updateUrl;

    // AJAX-Request, um die neue Stadienliste zu holen
    fetch(updateUrl)
        .then(response => response.json())
        .then(data => {
            // Update der Stadienliste im data-Attribute
            stadionInput.dataset.stadien = JSON.stringify(data);

            // Optional: Debugging
            console.log("Stadienliste aktualisiert:", data);
        })
        .catch(error => console.error('Fehler beim Aktualisieren der Stadienliste:', error));
}

// Beispielaufruf nach dem Anlegen eines neuen Stadions
document.getElementById('btn-neues-stadion').addEventListener('click', () => {
    updateStadiumList();
});


// Autocomplete für Stadionfeld
$(document).ready(function () {
    const availableStadien = JSON.parse($('#autocomplete-stadion').attr('data-stadien'));

    $('#autocomplete-stadion').autocomplete({
        source: availableStadien,
        select: function (event, ui) {
            $('#autocomplete-stadion').val(ui.item.klarname);
            $('#hidden-stadion-id').val(ui.item.value);
            return false;
        }
    });

    $('#autocomplete-stadion').blur(function () {
        if ($('#hidden-stadion-id').val() === '') {
            $('#autocomplete-stadion').val('');
        }
    });
});

// Autocomplete für Nachfolger
$(document).ready(function () {
	console.log("Data");
    const $nachfolgerInput = $('#autocomplete-nachfolger');
    const availableNachfolger = $nachfolgerInput.attr('data-nachfolger');
    console.log("Data-Nachfolger:", availableNachfolger);

    if (!availableNachfolger) {
        console.error("Das data-nachfolger-Attribut ist leer oder fehlt.");
        return;
    }

    $nachfolgerInput.autocomplete({
        source: JSON.parse(availableNachfolger),
        select: function (event, ui) {
            $nachfolgerInput.val(ui.item.klarname);
            $('#hidden-nachfolger-id').val(ui.item.value);
            return false;
        }
    });
});


function handleTypeChange(selectElement) {
    const typeId = parseInt(selectElement.value, 10); // Den aktuellen Typ abrufen
    const successorInput = document.getElementById('autocomplete-nachfolger');

    if (typeId === 6) {
        // Nachfolger-Input aktivieren
        successorInput.disabled = false;
		successorInput.focus(); // Setzt den Fokus auf das Feld
    } else {
        // Nachfolger-Input deaktivieren und leeren
        successorInput.disabled = true;
        successorInput.value = ''; // Optional: Inhalt löschen
        document.getElementById('hidden-nachfolger-id').value = ''; // Verstecktes Feld auch zurücksetzen
    }
}
