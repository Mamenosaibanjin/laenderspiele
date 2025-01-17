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

    if (saveButtonYouth) {
        saveButtonYouth.addEventListener('click', function () {
            savePlayerAssociation('youth'); // Speichern ohne Schließen
        });
    }

    document.body.addEventListener('click', function (event) {
    	console.log("Click");
        
        // Prüfen, ob auf einen Button mit der Klasse "save-button" geklickt wurde
        if (event.target && event.target.classList.contains('save-button')) {
        	console.log("save-button");
        	
            // Die nächste zugehörige Tabellenzeile ermitteln
            const row = event.target.closest('tr');

            if (row && row.dataset.id) {
        		
        		console.log("dataset");
                const rowId = row.dataset.id; // data-id der Zeile
                console.log(rowId);
                
                // Unterscheidung basierend auf Button-ID
                if (event.target && event.target.id === 'btn-save-clubs') {
                	console.log("btn-save-clubs");
                    savePlayerAssociation(rowId, 'club') // Spezifische Funktion für die Speicherung der Vereinskarriere aufrufen
                } else if (event.target && event.target.id === 'btn-save-nations') {
                	console.log("btn-save-nations");
                    savePlayerNations(rowId); // Funktion mit der Zeilen-ID aufrufen
                }
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

function savePlayerAssociation(rowId, type) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
     // Die Zeile anhand der rowId selektieren
    const row = document.querySelector(`[data-id="${rowId}"]`);
    if (!row) {
        alert("Zeile nicht gefunden!");
        return;
    }
    
    // Bestimme die relevanten HTML-Felder basierend auf dem Typ
    const isYouth = type === 'youth'; // Unterscheidung zwischen Jugend und Verein
    // Daten aus der Zeile extrahieren
    const associationData = {
        dataId: rowId, // Die ID der Zeile zur Referenzierung
        spielerID: getPlayerIDFromURL(), // SpielerID aus URL extrahieren
        vereinID: row.querySelector(isYouth ? '[name="youthVereinID"]' : '[name="vereinID"]').value,
        von: row.querySelector(isYouth ? '[name="youthVon"]' : '[name="von"]').value || null, // YYYYMM
        bis: row.querySelector(isYouth ? '[name="youthBis"]' : '[name="bis"]').value || null, // YYYYMM
        positionID: row.querySelector(isYouth ? '[name="youthPositionID"]' : '[name="positionID"]').value,
        jugend: isYouth ? 1 : 0 // Steuerung des Wertes für `jugend`
    };

    console.log("AssociationData", associationData);

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
            // Tabelle aktualisieren
            const spielerId = associationData.spielerID;
            const tableType = isYouth ? 'youth' : 'career';
            fetch(`http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/reload-${tableType}-table?spielerId=${spielerId}`)
                .then(response => response.text())
                .then(html => {
                    const tableSelector = isYouth ? '#youth-table tbody' : '#career-table tbody';
                    document.querySelector(tableSelector).innerHTML = html;
                })
                .catch(error => console.error('Fehler beim Laden der Tabelle:', error));
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

/**
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('verein-input');
    const hiddenInput = document.getElementById('vereinID');
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
Alte Awesomecplete Funktion

**/

document.addEventListener('DOMContentLoaded', function () {
	console.log('Start');
    const input = document.getElementById('autocomplete-verein');
    const hiddenInput = document.getElementById('hidden-verein-id');
    const vereinsDaten = JSON.parse(input.dataset.vereine);

	console.log('Vereinsdaten:', vereinsDaten); // Überprüfen, ob die Daten geladen werden

    // Autovervollständigung mit jQuery UI oder einer anderen Bibliothek
    $(input).autocomplete({
        source: vereinsDaten.map(item => ({
            label: item.label,
            value: item.value,
            klarname: item.klarname,
        })),
        select: function (event, ui) {
            // Setze den ausgewählten Namen und die ID
            input.value = ui.item.klarname;
            hiddenInput.value = ui.item.value;
            return false; // Verhindert, dass der Wert überschrieben wird
        },
        focus: function (event, ui) {
            // Zeige während der Navigation den Klarnamen an
            input.value = ui.item.klarname;
            return false;
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
					        <div class="form-group field-spielerverein-von">
					            <input type="month" class="form-control show-edit w-auto" name="SpielerVereinSaison[new][von]" style="width: 140px !important;" placeholder="Von">
					        </div>
					    </td>
					    <td>
					        <div class="form-group field-spielerverein-bis">
					            <input type="month" class="form-control show-edit w-auto" name="SpielerVereinSaison[new][bis]" style="width: 140px !important;" placeholder="Bis">
					        </div>
					    </td>
					    <td>
					        <div class="form-group field-spielerverein-verein">
					            <input type="text" class="form-control show-edit verein-input" list="vereine-datalist" name="SpielerVereinSaison[new][verein]" placeholder="Verein">
					            <input type="hidden" name="SpielerVereinSaison[new][vereinID]" value="">
					        </div>
					    </td>
					    <td>
					        <div class="form-group field-spielerverein-position">
					            <select class="form-control show-edit" name="SpielerVereinSaison[new][position]">
					                ${document.getElementById('position-options').innerHTML}
					            </select>
					        </div>
					    </td>
					    <td>
					            <div class="btn-group-toggle" data-toggle="buttons" style="float: left; padding-right: 7px;">
					                <input type="checkbox" name="SpielerVereinSaison[new][jugend]" id="jugend-switch-new" autocomplete="off" value="1">
					                <label for="jugend-switch-new" class="btn btn-secondary btn-sm" style="margin-left: 2px;">
					                    Jugend
					                </label>
					            </div>
					        <button type="submit" class="btn btn-primary btn-sm">Speichern</button>
					        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button>
					    </td>
					</tr>`;
					tableBody.insertAdjacentHTML('afterbegin', newRow);

					// Initialisieren der Event-Listener für neue Felder
					const addedRow = tableBody.querySelector('tr:first-child');
					if (addedRow) {
					    initializeRowEventListeners(addedRow);
					} else {
					    console.error("Neue Zeile wurde nicht erfolgreich hinzugefügt.");
					}

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

    document.querySelectorAll('.verein-input').forEach(input => {
        input.addEventListener('input', function () {
        console.log("Start");
            const datalist = document.getElementById('vereine-list');
            const hiddenField = this.closest('tr').querySelector('input[name="vereinID"]');
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