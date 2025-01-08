// Farben-Handling
$(document).ready(function () {
    const colors = $('#farben-input').val().split('-').filter(Boolean);

    function renderColors() {
        let html = '';
        colors.forEach((color, index) => {
            html += `
                <div class='color-item' style='display: inline-block; margin-right: 5px;'>
                    <input type='color' value='${color.startsWith("#") ? color : "#000011"}' class='color-picker'>
                    <button type='button' class='btn btn-danger btn-sm remove-color' data-index='${index}'>x</button>
                </div>`;
        });
        $('#color-picker-container').html(html); // Ersetzt statt anzuhängen
    }

    renderColors();

    $('#add-color').on('click', function () {
        colors.push('#000011');
        renderColors();
    });

    $(document).on('change', '.color-picker', function () {
        const index = $(this).closest('.color-item').index();
        colors[index] = $(this).val();
        $('#farben-input').val(colors.join('-'));
    });

    $(document).on('click', '.remove-color', function () {
        const index = $(this).data('index');
        colors.splice(index, 1);
        $('#farben-input').val(colors.join('-'));
        renderColors();
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
