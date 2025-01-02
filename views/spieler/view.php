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
    const saveButton = document.getElementById('btn-save-details');
    const saveEndButton = document.getElementById('btn-save-details-end');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (saveButton) {
        saveButton.addEventListener('click', function () {
            savePlayerDetails(false); // Speichern ohne Schließen
        });
    }

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

});
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
                            <button type="button" class="btn btn-secondary" onclick="toggleEditMode()">Abbrechen</button>
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
                        <h3>Vereinskarriere</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
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
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($karriere->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?></td>
		                                <?php if ($karriere->verein):?>
	                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($karriere->verein->id), ['alt' => $karriere->verein->name, 'style' => 'height: 30px;']) ?></td>
	                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>text-align: left;"><?= Html::a(Html::encode($karriere->verein->name), ['/club/view', 'id' => $karriere->verein->id], ['class' => 'text-decoration-none']) ?></td>
	                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::img(Helper::getFlagUrl($karriere->verein->land), ['alt' => $karriere->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
	                                    <?php else: ?>
	                                    	<td colspan="3"></td>
	                                    <?php endif; ?>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::encode($karriere->position->positionKurz) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                    </div>
                    <div class="card-body">
                        <table class="table">
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
                                            <?php
                                            if ($jugend->von || $jugend->bis) { // Nur ausgeben, wenn mindestens einer der Werte vorhanden ist
                                                if ($jugend->von && $jugend->bis) {
                                                    // Beide Werte vorhanden: Ausgabe von und bis
                                                    $von = DateTime::createFromFormat('Ym', $jugend->von)->format('Y');
                                                    $bis = DateTime::createFromFormat('Ym', $jugend->bis)->format('Y');
                                                    echo Html::encode($von === $bis ? $von : "$von - $bis");
                                                } elseif ($jugend->bis) {
                                                    // Nur bis vorhanden: Ausgabe nur bis
                                                    echo Html::encode(DateTime::createFromFormat('Ym', $jugend->bis)->format('Y'));
                                                }
                                            }
                                            ?>
                                        </td>
		                                <?php if ($jugend->verein):?>
                                            <td style="width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($jugend->verein->id), ['alt' => $jugend->verein->name, 'style' => 'height: 30px;']) ?></td>
                                            <td style="text-align: left;"><?= Html::encode($jugend->verein->name) ?></td>
                                            <td><?= Html::img(Helper::getFlagUrl($jugend->verein->land), ['alt' => $jugend->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                                        <?php else:?>
                                        	<td colspan="3"></td>
        	                            <?php endif;?>
                                        <td><?= Html::encode($jugend->position->positionKurz) ?></td>
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
                        <h3>Nationalmannschaftskarriere</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Wettbewerb</th>
                                    <th colspan="3">Nation</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laenderspiele as $spiel): ?>
                                    <tr>
                                        <td><?= Html::encode($spiel->wettbewerb->name) ?> <?= Html::encode($spiel->jahr) ?></td>
                                        <td style="width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($spiel->landID), ['alt' => Helper::getClubName($spiel->landID), 'style' => 'height: 30px;']) ?></td>
                                        <td style="text-align: left;"><?= Html::a(Html::encode(Helper::getClubName($spiel->landID)), ['/club/view', 'id' => $spiel->landID], ['class' => 'text-decoration-none']) ?></td>
                                        <td><?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->landID)), ['alt' => Helper::getClubName($spiel->landID), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                                        <td><?= Html::encode($spiel->position->positionKurz) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
