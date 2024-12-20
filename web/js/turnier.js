$(document).ready(function () {
    // Daten per AJAX laden
    $('#load-data-button').click(function () {
        let wettbewerbID = $('#wettbewerb-id').val();
        let jahr = $('#jahr-id').val();

        $.ajax({
            url: '/turnier/get-teams', // URL zur Controller-Aktion
            type: 'POST',
            data: {
                wettbewerbID: wettbewerbID,
                jahr: jahr,
                _csrf: yii.getCsrfToken() // CSRF-Token für Sicherheit
            },
            success: function (response) {
                // Erfolgreich - Inhalt einfügen
                $('#result-container').html(response);
            },
            error: function (xhr) {
                // Fehler - Meldung anzeigen
                alert('Ein Fehler ist aufgetreten: ' + xhr.responseText);
            },
        });
    });

    // Optional: Wettbewerbsoptionen beim Laden der Seite holen
    loadCompetitions();
    function loadCompetitions() {
        $.ajax({
            url: '/turnier/get-competitions',
            type: 'GET',
            success: function (response) {
                $('#wettbewerb-id').html(response);
            },
            error: function () {
                alert('Wettbewerbe konnten nicht geladen werden.');
            },
        });
    }
});
