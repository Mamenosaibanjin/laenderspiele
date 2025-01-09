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
