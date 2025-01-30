<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\ButtonHelper;
use app\components\Helper;
use app\components\SpielerHelper;
use app\models\Spieler;

/** @var $spieler app\models\Spieler */
/** @var $vereinsKarriere app\models\SpielerVereinSaison[] */
/** @var $jugendvereine app\models\SpielerVereinSaison[] */
/** @var $laenderspiele app\models\SpielerLandWettbewerb */

?>
<!-- Datalist für Vereine -->
<datalist id="vereine-datalist">
    <?php foreach ($vereine as $verein): ?>
        <option value="<?= Html::encode($verein->name) ?>" data-id="<?= $verein->id ?>"><?= Html::encode($verein->land) ?></option>
    <?php endforeach; ?>
</datalist>

<!-- Optionen für Positionen -->
<select id="position-options" style="display: none;">
    <?php foreach ($positionen as $position): ?>
        <option value="<?= $position->id ?>"><?= Html::encode($position->positionLang_de) ?></option>
    <?php endforeach; ?>
</select>
<?php
$this->registerJsFile('@web/js/spieler.js',  ['depends' => [\yii\web\JqueryAsset::class]]);

$isEditing = !(Yii::$app->user->isGuest); // Zustand für Bearbeitungsmodus
$this->title = $spieler->fullname ?? 'Spielerprofil';
$fields = [
    ['attribute' => 'vorname', 'icon' => 'fas fa-signature', 'options' => []],
    ['attribute' => 'name', 'icon' => 'fas fa-user', 'options' => []],
    ['attribute' => 'fullname', 'icon' => 'fas fa-address-card', 'options' => []],
    ['attribute' => 'geburtstag', 'icon' => 'fas fa-birthday-cake', 'options' => ['type' => 'date']],
    ['attribute' => 'nati1', 'icon' => 'fas fa-flag', 'options' => ['type' => 'dropdown']],
    ['attribute' => 'height', 'icon' => 'fas fa-ruler-vertical', 'options' => []],
    ['attribute' => 'weight', 'icon' => 'fas fa-weight', 'options' => []],
    ['attribute' => 'spielfuss', 'icon' => 'fas fa-shoe-prints', 'options' => ['type' => 'dropdown']],
    ['attribute' => 'homepage', 'icon' => 'fas fa-laptop-code', 'options' => []],
    ['attribute' => 'facebook', 'icon' => 'fa-brands fa-facebook', 'options' => []],
    ['attribute' => 'instagram', 'icon' => 'fa-brands fa-instagram', 'options' => []],
];
?>


<!-- Spieler-Seite: Header -->
<div class="container">
    <?php $form = ActiveForm::begin([
        'id' => 'spieler-form',
        'method' => 'post', // Wichtig: POST-Methode für Formulare
        'action' => ['spieler/' . ($spieler->id ?: 'new')],
    ]); ?>
    
    <!-- Widget 1: Allgemeine Spielerdaten -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <?php if ($spieler->id == 0): ?>
                            Neuer Spieler (#<?= Spieler::find()->max('id') + 1 ?>)
                        <?php else: ?>                        
                            <?= $isEditing ? "Bearbeiten: {$spieler->fullname}" : Html::encode(trim(($spieler->vorname ?? '') . ' ' . $spieler->name)) ?>
                            <?php if (!$isEditing && !Yii::$app->user->isGuest): ?>
                                <i class="fas fa-pen-to-square edit-button" style="cursor: pointer;" onclick="toggleEditMode()"></i>
                            <?php endif; ?>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <table class="table">
                            <?php foreach ($fields as $field): ?>
                                <?= SpielerHelper::renderEditableRow($form, $spieler, $field['attribute'], $field['icon'], $field['options']) ?>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <table class="table">
                            <?php foreach ($fields as $field): ?>
                                <?= SpielerHelper::renderViewRow($field['attribute'], $spieler, $field['icon']) ?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
<!-- Widget 2: Vereinskarriere -->
<?php if ($spieler->id > 0): ?>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Vereinskarriere</h3>
                </div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <table class="table" id="career-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Zeitraum</th>
                                    <th>Verein</th>
                                    <th>Position</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Leere Zeile für Neuanlage -->
                                <?= SpielerHelper::renderEditableRowMulti($form, new \app\models\SpielerVereinSaison(), ['von', 'bis', 'verein', 'position', 'buttons'], 'icon-class', [
                                    'index' => 'new',
                                    'positionen' => $positionen,
                                    'vereine' => $vereine,
                                ]); ?>
                                
                                <!-- Bestehende Einträge -->
                                <?php if (!empty($vereinsKarriere) || !empty($jugendvereine)): ?>
                                    <?php $gesamteKarriere = array_slice(array_merge($vereinsKarriere, $jugendvereine), 0, 20); ?>
                                    <?php foreach ($gesamteKarriere as $index => $karriere): ?>
                                        <?= Html::hiddenInput("SpielerVereinSaison[$index][id]", $karriere->id); ?>
                                        <?= SpielerHelper::renderEditableRowMulti($form, $karriere, ['von', 'bis', 'verein', 'position', 'buttons'], 'icon-class', [
                                            'index' => $index,
                                            'positionen' => $positionen,
                                            'vereine' => $vereine,
                                        ]); ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <button type="button" class="btn btn-primary mt-2" id="btn-neuer-verein" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/new', '_blank')">
                            neuer Verein
                        </button>
                    <?php else: ?>
                        <table class="table" id="career-table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th>Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= SpielerHelper::renderViewRowMulti($vereinsKarriere, ['zeitraum', 'verein', 'land', 'position'], ['index' => 0]); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    
    <div class="form-group">
        <?= ButtonHelper::saveButton() ?>
    </div>
    
    <?php ActiveForm::end(); ?>

	<?php if (!$isEditing): ?>               
        <!-- Widget 3: Jugendvereine -->
        <?php if ((!empty($jugendvereine)) && ($spieler->id > 0)): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Jugendvereine</h3>
                        </div>
                        <div class="card-body">
                            <table class="table" id="youth-club-table">
                                <thead>
                                    <tr>
                                        <th>Zeitraum</th>
                                        <th>Verein</th>
                                        <th>Land</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
							        <?= SpielerHelper::renderViewRowMulti($jugendvereine, ['zeitraum', 'verein', 'land', 'position'], ['index' => 0]); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif;?>

     <!-- Widget 4: Nationalmannschaftskarriere -->
    <?php if ((!empty($laenderspiele)) && ($spieler->id > 0)): ?>
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
                                                <button class="btn btn-secondary btn-sm cancel-button edit-mode">Abbrechen</button>
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
