<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Helper;
use app\models\Spieler;

/** @var $spieler app\models\Spieler */
/** @var $vereinsKarriere app\models\SpielerVereinSaison[] */
/** @var $jugendvereine app\models\SpielerVereinSaison[] */
/** @var $laenderspiele app\models\SpielerLandWettbewerb */

$this->title = $spieler->fullname ?? '';

$isEditing = !(Yii::$app->user->isGuest); // Zustand für Bearbeitungsmodus
$spieler = $spieler ?? new \app\models\Spieler(); // Spieler-Objekt für Neuanlage oder Bearbeitung
$flagOptions = ['' => 'Keine/Unbekannt'];
foreach (Helper::getAllFlags() as $code => $name) {
    $flagUrl = Helper::getFlagUrl($code);
    $flagOptions[$code] = '<img src="' . $flagUrl . '" style="width: 20px; height: 15px; margin-right: 8px;"> ' . Html::encode($name);
}

?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saveButtonDetails = document.getElementById('btn-save-details');
    const saveButtonClubs = document.getElementById('btn-save-clubs');
    const saveButtonYouth = document.getElementById('btn-save-youth');
    const saveButtonNations = document.getElementById('btn-save-nations');
    
	const saveEndButton = document.getElementById('btn-save-details-end');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (saveButtonDetails) {
        saveButtonDetails.addEventListener('click', function () {
            savePlayerDetails(false); // Speichern ohne Schließen
        });
    }

    if (saveButtonClubs) {
        saveButtonClubs.addEventListener('click', function () {
        alert("Club");
            savePlayerAssociation('club'); // Speichern ohne Schließen
        });
    }

    if (saveButtonYouth) {
        saveButtonYouth.addEventListener('click', function () {
            savePlayerAssociation('youth'); // Speichern ohne Schließen
        });
    }

    document.body.addEventListener('click', function (event) {
        // Prüfen, ob auf einen Button mit der Klasse "save-button" geklickt wurde
        if (event.target && event.target.classList.contains('save-button')) {
            // Die nächste zugehörige Tabellenzeile ermitteln
            const row = event.target.closest('tr');

            if (row && row.dataset.id) {
                const rowId = row.dataset.id; // data-id der Zeile
                savePlayerNations(rowId); // Funktion mit der Zeilen-ID aufrufen
            } else {
                console.error('Keine gültige Zeile oder data-id gefunden.');
            }
        }
    });


    if (saveEndButton) {
        saveEndButton.addEventListener('click', function () {
            savePlayerDetails(true); // Speichern und Schließen
        });
    }

    // Funktion zum Extrahieren der playerID aus der URL
    function getPlayerIDFromURL() {
        const url = window.location.pathname; // Der komplette Pfad der URL
        const matches = url.match(/\/spieler\/(\d+)$/); // Sucht nach der playerID (Ziffern am Ende der URL)
        return matches ? matches[1] : null; // Gibt die playerID zurück oder null, falls keine gefunden wurde
    }

    function savePlayerDetails() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const playerID = getPlayerIDFromURL(); // Extrahiert die playerID aus der URL
    
    const playerData = {
        playerID: playerID, // Hier die playerID einfügen
        name: document.getElementById('name').value || '', // Standardwert für leere Felder
        vorname: document.getElementById('vorname').value || '',
        fullname: document.getElementById('fullname').value || '',
        geburtsort: document.getElementById('geburtsort').value || '',
        geburtsland: document.getElementById('geburtsland').value || '',
        geburtstag: document.getElementById('geburtstag').value || '', // Leer lassen, wenn kein Wert
        height: document.getElementById('height').value || null, // Null setzen, wenn leer
        weight: document.getElementById('weight').value || null,
        spielfuss: document.getElementById('spielfuss').value || null,
        homepage: document.getElementById('homepage').value || null, // Null für leere URLs
        facebook: document.getElementById('facebook').value || null,
        instagram: document.getElementById('instagram').value || null,
        nati1: document.getElementById('nati1').value || '',
        nati2: document.getElementById('nati2').value || null,
        nati3: document.getElementById('nati3').value || null
    };
    
    // Sicherstellen, dass das Formular vollständig ist
    const requiredFields = ['name', 'fullname', 'nati1'];
    for (let field of requiredFields) {
        if (!playerData[field]) {
            alert(`${field} ist erforderlich!`);
            return;
        }
    }

    // Ajax-Request zum Speichern der Daten
    fetch('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(playerData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Spieler erfolgreich gespeichert!');
        } else {
            alert('Fehler beim Speichern des Spielers.');
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Es gab ein Problem beim Speichern der Spielerinformationen.');
    });
}

function savePlayerAssociation(type) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const playerID = getPlayerIDFromURL(); // Extrahiert die playerID aus der URL

    // Bestimme die relevanten HTML-Felder basierend auf dem Typ
    const isYouth = type === 'youth'; // Unterscheidung zwischen Jugend und Verein
    const vereinIDField = isYouth ? 'youthVereinID' : 'vereinID';
    const vonField = isYouth ? 'youthVon' : 'von';
    const bisField = isYouth ? 'youthBis' : 'bis';
    const positionIDField = isYouth ? 'youthPositionID' : 'positionID';

    const associationData = {
        spielerID: playerID,
        vereinID: document.getElementById(vereinIDField).value,
        von: document.getElementById(vonField).value || null, // YYYYMM
        bis: document.getElementById(bisField).value || null, // YYYYMM
        positionID: document.getElementById(positionIDField).value,
        jugend: isYouth ? 1 : 0 // Steuerung des Wertes für `jugend`
    };

    // Prüfen, ob die erforderlichen Felder ausgefüllt sind
    const requiredFields = ['vereinID', 'positionID'];
    for (let field of requiredFields) {
        if (!associationData[field]) {
            alert(`${field} ist erforderlich!`);
            return;
        }
    }

    const url = isYouth 
        ? 'http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/save-youth' 
        : 'http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/save-club';

    // Senden der Daten
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(associationData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`${isYouth ? 'Jugendvereinsdaten' : 'Vereinsdaten'} erfolgreich gespeichert!`);
        } else {
            alert(`Fehler beim Speichern der ${isYouth ? 'Jugendvereinsdaten' : 'Vereinsdaten'}.`);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert(`Es gab ein Problem beim Speichern der ${isYouth ? 'Jugendvereinsdaten' : 'Vereinsdaten'}.`);
    });
}

function savePlayerNations(rowId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log("rowId");
                console.log(rowId);

    // Die Zeile anhand der rowId selektieren
    const row = document.querySelector(`[data-id="${rowId}"]`);
    if (!row) {
        alert("Zeile nicht gefunden!");
        return;
    }

    // Daten aus der Zeile extrahieren
    const nationData = {
        dataId: row.dataset.id,
        spielerID: row.dataset.id.split('-')[0], // SpielerID aus data-id extrahieren
        wettbewerbID: row.querySelector('[name="wettbewerbID"]').value,
        landID: row.querySelector('[name="landID"]').value,
        positionID: row.querySelector('[name="positionID"]').value,
        jahr: row.querySelector('[name="jahr"]').value,
        land: row.querySelector('[name="land"]').value,
    };
    
    // Debug-Ausgabe der Daten
    console.log("NationData");
    console.log(nationData);

    // Prüfen, ob die erforderlichen Felder ausgefüllt sind
    const requiredFields = ['wettbewerbID', 'landID', 'positionID', 'jahr'];
    for (let field of requiredFields) {
        if (!nationData[field]) {
            alert(`${field} ist erforderlich!`);
            return;
        }
    }

    fetch('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/save-nation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(nationData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Nationalmannschaftsdaten erfolgreich gespeichert!');
        } else {
            alert('Fehler beim Speichern der Nationalmannschaftsdaten.');
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Es gab ein Problem beim Speichern der Nationalmannschaftsdaten.');
    });
}


});

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('verein-input');
    const hiddenInput = document.getElementById('verein-id');
    const options = [...document.querySelectorAll('#vereine-list option')];

    // Awesomplete initialisieren
    new Awesomplete(input, {
        list: options.map(option => option.value),
        minChars: 1,
        autoFirst: true
    });

    // VereinID setzen, wenn ein Name ausgewählt wird
    input.addEventListener('awesomplete-selectcomplete', function () {
        const selectedOption = options.find(option => option.value === input.value);
        if (selectedOption) {
            hiddenInput.value = selectedOption.dataset.id;
        } else {
            hiddenInput.value = ''; // Keine gültige Auswahl
        }
    });

    // VereinID zurücksetzen, falls der Benutzer den Text ändert
    input.addEventListener('input', function () {
        const selectedOption = options.find(option => option.value === input.value);
        if (!selectedOption) {
            hiddenInput.value = '';
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const tableIds = ['career-table', 'youth-club-table', 'national-team-table'];

    tableIds.forEach((tableId) => {
        const table = document.getElementById(tableId);

        if (!table) {
            console.error(`Tabelle '${tableId}' nicht gefunden!`);
            return;
        }

        const tableBody = table.querySelector('tbody');
        if (!tableBody) {
            console.error(`Tabelle '${tableId}' hat keinen <tbody>!`);
            return;
        }

        // Initialisieren der Event-Listener für vorhandene Zeilen
        tableBody.querySelectorAll('tr').forEach(initializeRowEventListeners);

        // Hinzufügen-Button
        const addButton = document.getElementById(`add-${tableId.replace('-table', '')}-entry`);
        if (addButton) {
            addButton.addEventListener('click', () => {
 let newRow = '';

                if (tableId === 'national-team-table') {
                    // Spezifische Struktur für die Nationalmannschaft
                    newRow = `
                        <tr>
                            <td>
                                <select class="form-control show-edit" name="wettbewerbID" id="wettbewerbID" style="width: 150px;">
                                    <?php foreach ($wettbewerbe as $wettbewerb): ?>
                                        <option value="<?= $wettbewerb->id ?>">
                                            <?= Html::encode($wettbewerb->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" class="form-control show-edit w-auto" name="jahr" id="jahr" value="" style="width: 150px !important;" />
                            </td>
                            <td style="width: 35px; text-align: right;">
                                <!-- Placeholder für Nation-Logo -->
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control show-edit nation-input" id="nation-input" list="nationen-list" autocomplete="off" />
                                <input type="hidden" name="landID" id="landID" value="" />
                                <datalist id="nationen-list">
                                    <?php foreach ($nationen as $nation): ?>
                                        <option value="<?= Html::encode($nation->name) ?>" data-id="<?= $nation->id ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </td>
                            <td>
                                <select class="form-control show-edit" name="positionID" id="positionID">
                                    <?php foreach ($positionen as $position): ?>
                                        <option value="<?= $position->id ?>">
                                            <?= Html::encode($position->positionLang_de) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm save-button show-edit">Speichern</button>
                                <button class="btn btn-secondary btn-sm cancel-button show-edit">Abbrechen</button>
                            </td>
                        </tr>`;
                } else {
                    // Generische Struktur für andere Tabellen
                    newRow = `
                        <tr>
                            <td>
                                <input type="month" class="form-control show-edit w-auto" name="von">
                                <input type="month" class="form-control show-edit w-auto" name="bis">
                            </td>
                            <td></td>
                            <td>
                                <input type="text" class="form-control show-edit" list="${tableId}-list" style="width: 175px;">
                                <input type="hidden" name="vereinID" value="">
                                <datalist id="${tableId}-list">
                                    <?php foreach ($vereine as $verein): ?>
                                        <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </td>
                            <td></td>
                            <td>
                                <select class="form-control show-edit" name="positionID">
                                    <?php foreach ($positionen as $position): ?>
                                        <option value="<?= $position->id ?>"><?= Html::encode($position->positionLang_de) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm save-button show-edit">Speichern</button>
                                <button class="btn btn-secondary btn-sm cancel-button show-edit">Abbrechen</button>
                            </td>
                        </tr>`;
                }
                tableBody.insertAdjacentHTML('afterbegin', newRow);

                // Initialisieren der Event-Listener für die neue Zeile
                const addedRow = tableBody.querySelector('tr:first-child');
                if (addedRow) {
                    initializeRowEventListeners(addedRow);
                } else {
                    console.error("Neue Zeile wurde nicht erfolgreich hinzugefügt.");
                }
            });
        }
    });
    
    document.querySelectorAll('.nation-input').forEach(input => {
        input.addEventListener('input', function () {
        console.log("Start");
            const datalist = document.getElementById('nationen-list');
            const hiddenField = this.closest('tr').querySelector('input[name="landID"]');
            const selectedOption = Array.from(datalist.options).find(option => option.value === this.value);
    
            if (selectedOption) {
                // Aktualisiere das Hidden-Feld mit dem data-id-Wert der Option
                hiddenField.value = selectedOption.dataset.id;
            } else {
                // Falls der Wert nicht in der Datalist ist, setze das Hidden-Feld zurück
                hiddenField.value = '';
            }
        });
	});
});


// Funktion zum Initialisieren der Event-Listener für eine Zeile
function initializeRowEventListeners(row) {
    if (!row) return; // Sicherstellen, dass die Zeile existiert

    // Bearbeiten-Button
    const editButton = row.querySelector('.edit-button');
    if (editButton) {
        editButton.addEventListener('click', () => {
            toggleEditMode(row, true);
        });
    }

    // Speichern-Button
    const saveButton = row.querySelector('.save-button');
    if (saveButton) {
        saveButton.addEventListener('click', () => {
            // Hier Speichern-Logik hinzufügen (z.B. Formular absenden oder Daten speichern)
            toggleEditMode(row, false);
        });
    }

    // Abbrechen-Button
    const cancelButton = row.querySelector('.cancel-button');
    if (cancelButton) {
        cancelButton.addEventListener('click', () => {
            // Änderungen zurücksetzen und Bearbeitungsmodus verlassen
            toggleEditMode(row, false);
        });
    }
}

// Funktion zum Umschalten des Bearbeitungsmodus
function toggleEditMode(row, isEditing) {
    if (!row) return; // Sicherstellen, dass die Zeile existiert

    if (isEditing) {
        // Anzeige-Modus ausblenden
        row.querySelectorAll('.display-mode').forEach(el => {
            el.classList.add('hide-display');
            el.classList.remove('display-mode');
        });

        // Bearbeiten-Modus einblenden
        row.querySelectorAll('.edit-mode').forEach(el => {
            el.classList.add('show-edit');
            el.classList.remove('edit-mode');
        });

        // Buttons anpassen
        row.querySelector('.edit-button').style.display = 'none';
        row.querySelector('.save-button').style.display = 'inline-block';
        row.querySelector('.cancel-button').style.display = 'inline-block';
    } else {
        // Anzeige-Modus einblenden
        row.querySelectorAll('.hide-display').forEach(el => {
            el.classList.add('display-mode');
            el.classList.remove('hide-display');
        });

        // Bearbeiten-Modus ausblenden
        row.querySelectorAll('.show-edit').forEach(el => {
            el.classList.add('edit-mode');
            el.classList.remove('show-edit');
        });

        // Buttons anpassen
        row.querySelector('.edit-button').style.display = 'inline-block';
        row.querySelector('.save-button').style.display = 'none';
        row.querySelector('.cancel-button').style.display = 'none';
    }
}


</script>

<!-- Spieler-Seite: Header -->
<div class="container">
    <!-- Widget 1: Allgemeine Spielerdaten -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <?php if ($spieler->id == 0): ?>
                            Neuer Spieler (#<?= Spieler::find()->max('id') + 1 ?>)
                        <?php else: ?>                        <?= $isEditing ? "Bearbeiten: {$spieler->fullname}" : Html::encode(trim(($spieler->vorname ?? '') . ' ' . $spieler->name)) ?>
                            <?php if (!$isEditing && !Yii::$app->user->isGuest): ?>
                                <i class="fas fa-pen-to-square edit-button" style="cursor: pointer;" onclick="toggleEditMode()"></i>
                            <?php endif; ?>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form id="spielerForm" action="<?= Url::to(['spieler/save']) ?>" method="POST">
                        <?= Html::hiddenInput('_csrf', Yii::$app->request->csrfToken) ?>
                        <table class="table">
                            <!-- Vorname -->
                            <?php if ($isEditing || !empty($spieler->vorname)): ?>
                                <tr>
                                    <th><i class="fas fa-signature"></i></th>
                                    <td>
                                        <?= $isEditing
                                            ? Html::textInput('vorname', $spieler->vorname, ['class' => 'form-control', 'id' => 'vorname'])
                                            : Html::encode($spieler->vorname) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Nachname -->
                            <?php if ($isEditing || !empty($spieler->name)): ?>
                                <tr>
                                    <th><i class="fas fa-shirt"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::textInput('name', $spieler->name, ['class' => 'form-control', 'id' => 'name'])
                                            : Html::encode($spieler->name) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Vollständiger Name -->
                            <?php if ($isEditing || !empty($spieler->fullname)): ?>
                                <tr>
                                    <th><i class="fas fa-address-card"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::textInput('fullname', $spieler->fullname, ['class' => 'form-control', 'id' => 'fullname'])
                                            : Html::encode($spieler->fullname) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Geburtstag -->
                            <?php if ($isEditing || !empty($spieler->geburtstag)): ?>
                                <tr>
                                    <th><i class="fas fa-birthday-cake"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::input('date', 'geburtstag', $spieler->geburtstag, ['class' => 'form-control', 'id' => 'geburtstag'])
                                            : ($spieler->geburtstag ? Yii::$app->formatter->asDate($spieler->geburtstag, 'dd.MM.yyyy') : '') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <!-- Geburtsort und Geburtsland -->
                            <?php if ($isEditing || !empty($spieler->geburtsort) || !empty($spieler->geburtsland)): ?>
                                <tr>
                                    <th><i class="fas fa-map-marker-alt"></i></th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?= $isEditing
                                            ? Html::textInput('geburtsort', $spieler->geburtsort, ['class' => 'form-control me-2', 'id' => 'geburtsort'])
                                                : Html::encode($spieler->geburtsort) ?>
                                            <?= $isEditing
                                                ? Html::dropDownList('geburtsland', $spieler->geburtsland, $flagOptions, [
                                                    'class' => 'form-control selectpicker', // Bootstrap Select-Klasse
                                                    'encode' => false, // HTML in den Optionen erlauben
                                                    'id' => 'geburtsland',
                                                    'data-live-search' => 'true' // Suchfunktion aktivieren
                                                ])
                                                : (!empty($spieler->geburtsland)
                                                    ? Html::img(Helper::getFlagUrl($spieler->geburtsland, $spieler->geburtstag), [
                                                        'alt' => $spieler->geburtsland,
                                                        'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-left: 8px;'
                                                    ])
                                                    : '') ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <!-- Nationalitäten -->
                            <?php if ($isEditing || !empty($spieler->nati1) || !empty($spieler->nati2) || !empty($spieler->nati3)): ?>
                                <tr>
                                    <th><i class="fas fa-flag"></i></th>
                                    <td>
                                        <div class="d-flex">
                                            <?php foreach (['nati1', 'nati2', 'nati3'] as $nati): ?>
                                                <?= $isEditing
                                                    ? Html::dropDownList($nati, $spieler->$nati, $flagOptions, [
                                                        'class' => 'form-control selectpicker me-2', // Bootstrap Select-Klasse
                                                        'encode' => false, // HTML in den Optionen erlauben
                                                        'id' => $nati,
                                                        'data-live-search' => 'true' // Suchfunktion aktivieren
                                                    ])
                                                    : (!empty($spieler->$nati)
                                                        ? Html::img(Helper::getFlagUrl($spieler->$nati), [
                                                            'alt' => $spieler->$nati,
                                                            'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;'
                                                        ])
                                                        : '') ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                           <!-- Größe -->
                            <?php if ($isEditing || !empty($spieler->height)): ?>
                                <tr>
                                    <th><i class="fas fa-ruler-vertical"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::input('number', 'height', $spieler->height, ['class' => 'form-control', 'min' => 100, 'max' => 250, 'id' => 'height'])
                                            : Html::encode($spieler->height) . ' cm' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Gewicht -->
                            <?php if ($isEditing || !empty($spieler->weight)): ?>
                                <tr>
                                    <th><i class="fas fa-weight-hanging"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::input('number', 'weight', $spieler->weight, ['class' => 'form-control', 'min' => 30, 'max' => 150, 'id' => 'weight'])
                                            : Html::encode($spieler->weight) . ' kg' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Spielfuß -->
                            <?php if ($isEditing || !empty($spieler->spielfuss)): ?>
                                <tr>
                                    <th><i class="fas fa-shoe-prints"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::dropDownList('spielfuss', $spieler->spielfuss, ['' => 'unbekannt', 'L' => 'Links', 'R' => 'Rechts', 'B' => 'Beidfüßig'], ['class' => 'form-control', 'id' => 'spielfuss'])
                                            : Html::encode($spieler->spielfuss) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Homepage -->
                            <?php if ($isEditing || !empty($spieler->homepage)): ?>
                                <tr>
                                    <th><i class="fas fa-laptop-code"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::textInput('homepage', $spieler->homepage, ['class' => 'form-control', 'id' => 'homepage'])
                                            : Html::a($spieler->homepage, 'http://' . $spieler->homepage, ['target' => '_blank']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Instagram -->
                            <?php if ($isEditing || !empty($spieler->instagram)): ?>
                                <tr>
                                    <th><i class="fa-brands fa-instagram"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::textInput('instagram', $spieler->instagram, ['class' => 'form-control', 'id' => 'instagram'])
                                            : Html::a($spieler->instagram, 'http://www.instagram.com/' . $spieler->instagram, ['target' => '_blank']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
    
                            <!-- Facebook -->
                            <?php if ($isEditing || !empty($spieler->facebook)): ?>
                                <tr>
                                    <th><i class="fa-brands fa-facebook"></i></th>
                                    <td>
                                        <?= $isEditing
                                        ? Html::textInput('facebook', $spieler->facebook, ['class' => 'form-control', 'id' => 'facebook'])
                                            : Html::a($spieler->facebook, 'http://www.facebook.com/' . $spieler->facebook, ['target' => '_blank']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
    
                        <?php if ($isEditing): ?>
                            <button type="button" class="btn btn-primary" id="btn-save-details">Speichern</button>
                            <button type="button" class="btn btn-secondary">Abbrechen</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
	</div>


    <!-- Widget 2: Vereinskarriere -->
    <?php
    $currentMonth = date('Ym'); // Aktueller Monat im Format 'YYYYMM'
    ?>
    <?php if ((!empty($vereinsKarriere)) && ($spieler->id > 0 || 1 == 1)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Vereinskarriere 
                        	<?php if ($isEditing) : ?>
                        		<button class="btn btn-secondary btn-sm" id="add-career-entry">+</button>
                        	<?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table" id="career-table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th colspan="2">Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vereinsKarriere as $karriere): ?>
                                    <tr>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
	                                       	<span class="display-mode">
	                                       		<?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($karriere->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?>
	                                       	</span>
                                        	<?php if ($isEditing): ?>
                                        		<input type="month" class="form-control edit-mode w-auto" name="von" value="<?= substr($karriere->von, 0, 4) . '-' . substr($karriere->von, 4, 2) ?>">
                                    			<input type="month" class="form-control edit-mode w-auto" name="bis" value="<?= substr($karriere->bis, 0, 4) . '-' . substr($karriere->bis, 4, 2) ?>">
                                           	<?php endif; ?>
                                        </td>
                                        
		                                <?php if ($karriere->verein): ?>
                                            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>width: 35px; text-align: right;">
                                                <span class="display-mode">
                                                    <?= Html::img(Helper::getClubLogoUrl($karriere->verein->id), ['alt' => $karriere->verein->name, 'style' => 'height: 30px;']) ?>
                                                </span>
                                            </td>
                                            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>text-align: left;">
                                                <span class="display-mode">
                                                    <?= Html::a(Html::encode($karriere->verein->name), ['/club/view', 'id' => $karriere->verein->id], ['class' => 'text-decoration-none']) ?>
                                                </span>
                                                <?php if ($isEditing): ?>
                                                    <input type="text" class="form-control edit-mode" id="verein-input" list="vereine-list" value="<?= Html::encode($karriere->verein->name ?? '') ?>" autocomplete="off" style="width: 175px;">
                                                    <input type="hidden" name="vereinID" id="verein-id" value="<?= Html::encode($karriere->vereinID) ?>">
                                                    
                                                    <datalist id="vereine-list">
                                                        <?php foreach ($vereine as $verein): ?>
                                                            <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                        <?php endforeach; ?>
                                                    </datalist>
                                                <?php endif; ?>
                                            </td>
                                            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>">
                                                <span class="display-mode">
                                                    <?= Html::img(Helper::getFlagUrl($karriere->verein->land), ['alt' => $karriere->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                                                </span>
                                            </td>
                                        <?php else: ?>
                                            <td colspan="3">
                                                <span class="display-mode"></span>
                                                <?php if ($isEditing): ?>
                                                    <div class="edit-mode" style="display: block;">
                                                        <input type="text" class="form-control" id="verein-input" list="vereine-list" value="" autocomplete="off" style="width: 175px;">
                                                        <input type="hidden" name="vereinID" id="verein-id" value="">
                                                        
                                                        <datalist id="vereine-list">
                                                            <?php foreach ($vereine as $verein): ?>
                                                                <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                            <?php endforeach; ?>
                                                        </datalist>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>

                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
                                        	<span class="display-mode">
                                        		<?= Html::encode($karriere->position->positionKurz) ?>
                                        	</span>
                                        	<?php if ($isEditing): ?>
	                                        	<select class="form-control edit-mode" name="positionID">
                                                    <?php foreach ($positionen as $position): ?>
                                                        <option value="<?= $position->id ?>" <?= $karriere->positionID == $position->id ? 'selected' : '' ?>><?= Html::encode($position->positionLang_de) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isEditing): ?>
    										<td>
                                                <button class="btn btn-primary btn-sm edit-button display-mode">Bearbeiten</button>
                                                <button class="btn btn-primary btn-sm save-button edit-mode" style="display: none;" id="btn-save-clubs">Speichern</button>
                                                <button class="btn btn-secondary btn-sm cancel-button edit-mode" style="display: none;">Abbrechen</button>
                                            </td>
                                        <?php endif; ?>                                    
                                	</tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <button type="button" class="btn btn-primary mt-2" id="btn-neuer-verein" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/new', '_blank')">
                        	neuer Verein
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
                              
    <!-- Widget 3: Jugendvereine -->
    <?php if ((!empty($jugendvereine)) && ($spieler->id > 0 || 1 == 1)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Jugendvereine</h3>
                        <?php if ($isEditing): ?>
                            <button class="btn btn-secondary btn-sm" id="add-youth-club-entry">+</button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <table class="table" id="youth-club-table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th colspan="2">Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jugendvereine as $jugend): ?>
                                    <tr>
                                        <td>
	                                       	<span class="display-mode">
	                                       		<?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($jugend->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?>
	                                       	</span>
                                        	<?php if ($isEditing): ?>
                                        		<input type="month" class="form-control edit-mode w-auto" name="von" value="<?= substr($jugend->von, 0, 4) . '-' . substr($jugend->von, 4, 2) ?>">
                                    			<input type="month" class="form-control edit-mode w-auto" name="bis" value="<?= substr($jugend->bis, 0, 4) . '-' . substr($jugend->bis, 4, 2) ?>">
                                           	<?php endif; ?>
                                        </td>
                                        
                                        <?php if ($jugend->verein): ?>
                                            <td style="width: 35px; text-align: right;">
                                                <span class="display-mode">
                                                    <?= Html::img(Helper::getClubLogoUrl($jugend->verein->id), ['alt' => $jugend->verein->name, 'style' => 'height: 30px;']) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: left;">
                                                <span class="display-mode">
                                                    <?= Html::encode($jugend->verein->name) ?>
                                                </span>
                                                <?php if ($isEditing): ?>
                                                    <input type="text" class="form-control edit-mode" id="youth-club-input" list="youth-club-list" value="<?= Html::encode($jugend->verein->name) ?>" autocomplete="off" style="width: 175px;">
                                                    <input type="hidden" name="vereinID" id="verein-id" value="<?= $jugend->vereinID ?>">
                                                    <datalist id="youth-club-list">
                                                        <?php foreach ($vereine as $verein): ?>
                                                            <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                        <?php endforeach; ?>
                                                    </datalist>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="display-mode">
                                                    <?= Html::img(Helper::getFlagUrl($jugend->verein->land), ['alt' => $jugend->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                                                </span>
                                            </td>
                                        <?php else: ?>
                                            <td colspan="3">
                                                <span class="display-mode"></span>
                                                <?php if ($isEditing): ?>
                                                    <div class="edit-mode" style="display: block;">
                                                        <input type="text" class="form-control" id="youth-club-input" list="youth-club-list" value="" autocomplete="off" style="width: 175px;">
                                                        <input type="hidden" name="vereinID" id="verein-id" value="">
                                                        <datalist id="youth-club-list">
                                                            <?php foreach ($vereine as $verein): ?>
                                                                <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                            <?php endforeach; ?>
                                                        </datalist>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
    
                                        <td>
                                            <span class="display-mode">
                                                <?= Html::encode($jugend->position->positionKurz) ?>
                                            </span>
                                            <?php if ($isEditing): ?>
                                                <select class="form-control edit-mode" name="positionID">
                                                    <?php foreach ($positionen as $position): ?>
                                                        <option value="<?= $position->id ?>" <?= $jugend->positionID == $position->id ? 'selected' : '' ?>>
                                                            <?= Html::encode($position->positionLang_de) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isEditing): ?>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-button display-mode">Bearbeiten</button>
                                                <button class="btn btn-primary btn-sm save-button edit-mode" style="display: none;" id="btn-save-youth">Speichern</button>
                                                <button class="btn btn-secondary btn-sm cancel-button edit-mode" style="display: none;">Abbrechen</button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

     <!-- Widget 4: Nationalmannschaftskarriere -->
    <?php if ((!empty($laenderspiele)) && ($spieler->id > 0 || 1 == 1)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Nationalmannschaftskarriere
                            <?php if ($isEditing) : ?>
                                <button class="btn btn-secondary btn-sm" id="add-national-team-entry">+</button>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table" id="national-team-table">
                            <thead>
                                <tr>
                                    <th>Wettbewerb</th>
                                    <th colspan="2">Nation</th>
                                    <th>Position</th>
                                    <?php if ($isEditing): ?>
                                        <th>Aktionen</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laenderspiele as $spiel): ?>
                                	<?php $dataid = $spieler->id . '-' . $spiel->landID . '-' . $spiel->wettbewerbID . '-' . $spiel->land . '-' . $spiel->jahr;?>
                                    <tr data-id='<?= $dataid ?>'> 
                                        <td>
                                            <span class="display-mode">
                                                <?= Html::encode($spiel->wettbewerb->name) ?> <?= Html::encode($spiel->jahr) ?>
                                            </span>
                                            <?php if ($isEditing): ?>
                                                <select class="form-control edit-mode" name="wettbewerbID"  id="wettbewerbID" style="width: 180px;">
                                                    <?php foreach ($wettbewerbe as $wettbewerb): ?>
                                                        <option value="<?= $wettbewerb->id ?>" <?= $spiel->wettbewerb->id == $wettbewerb->id ? 'selected' : '' ?>>
                                                            <?= Html::encode($wettbewerb->name) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="number" class="form-control edit-mode w-auto" name="jahr" id="jahr" value="<?= Html::encode($spiel->jahr) ?>" style="width: 150px !important;"/>
                                                <?= Html::hiddenInput('land', $spiel->land) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td style="width: 35px; text-align: right;">
                                            <span class="display-mode">
                                                <?= Html::img(Helper::getClubLogoUrl($spiel->landID), ['alt' => Helper::getClubName($spiel->landID), 'style' => 'height: 30px;']) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: left;">
                                            <span class="display-mode">
                                                <?= Html::a(Html::encode(Helper::getClubName($spiel->landID)), ['/club/view', 'id' => $spiel->landID], ['class' => 'text-decoration-none']) ?>
                                            </span>
                                            <?php if ($isEditing): ?>
                                                <input type="text" class="form-control edit-mode nation-input" id="nation-input" list="nationen-list" value="<?= Html::encode(Helper::getClubName($spiel->landID)) ?>" autocomplete="off" />
                                                <input type="hidden" name="landID" id="landID" value="<?= Html::encode($spiel->landID) ?>" />
                                                
                                                <datalist id="nationen-list">
                                                    <?php foreach ($nationen as $nation): ?>
                                                        <option value="<?= Html::encode($nation->name) ?>" data-id="<?= $nation->id ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="display-mode">
                                                <?= Html::encode($spiel->position->positionKurz) ?>
                                            </span>
                                            <?php if ($isEditing): ?>
                                                <select class="form-control edit-mode" name="positionID" id="positionID">
                                                    <?php foreach ($positionen as $position): ?>
                                                        <option value="<?= $position->id ?>" <?= $spiel->positionID == $position->id ? 'selected' : '' ?>>
                                                            <?= Html::encode($position->positionLang_de) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isEditing): ?>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-button display-mode">Bearbeiten</button>
                                                <button class="btn btn-primary btn-sm save-button edit-mode" id="btn-save-nations">Speichern</button>
                                                <button class="btn btn-secondary btn-sm cancel-button edit-mode" style="display: none;">Abbrechen</button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if ($isEditing): ?>
                            <button type="button" class="btn btn-primary mt-2" id="btn-neue-nation" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/new', '_blank')">
                                neue Nation
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
