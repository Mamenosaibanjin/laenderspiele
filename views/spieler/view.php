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
                                <?= SpielerHelper::renderEditableRowMulti($form, new \app\models\SpielerVereinSaison(), ['von', 'bis', 'verein', 'position', 'buttonsMitJugend'], 'icon-class', [
                                    'index' => 'new',
                                    'positionen' => $positionen,
                                    'vereine' => $vereine,
                                ]); ?>
                                
                                <!-- Bestehende Einträge -->
                                <?php if (!empty($vereinsKarriere) || !empty($jugendvereine)): ?>
                                    <?php $gesamteKarriere = array_slice(array_merge($vereinsKarriere, $jugendvereine), 0, 20); ?>
                                    <?php foreach ($gesamteKarriere as $index => $karriere): ?>
                                        <?= Html::hiddenInput("SpielerVereinSaison[$index][id]", $karriere->id); ?>
                                        <?= SpielerHelper::renderEditableRowMulti($form, $karriere, ['von', 'bis', 'verein', 'position', 'buttonsMitJugend'], 'icon-class', [
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
    <?php if (((!empty($laenderspiele)) && ($spieler->id > 0)) || ($isEditing&& ($spieler->id > 0))): ?>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Nationalmannschaftskarriere</h3>
                </div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <table class="table" id="national-team-table">
                            <thead>
                                <tr>
                                    <th>Wettbewerb</th>
                                    <th>Nation</th>
                                    <th>Position</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Leere Zeile für Neuanlage -->
                                <?= SpielerHelper::renderEditableRowMulti($form, new \app\models\SpielerLandWettbewerb(), ['wettbewerb', 'nation', 'position', 'buttons'], 'icon-class', [
                                    'index' => 'new',
                                    'positionen' => $positionen,
                                    'nationen' => $nationen,
                                    'wettbewerbe' => $wettbewerbe,
                                    'tournaments' => $tournaments,
                                ]); ?>
                                
                                <!-- Bestehende Einträge -->
                                <?php if (!empty($laenderspiele)): ?>
                                    <?php foreach ($laenderspiele as $index => $spiel): ?>
                                        <?= Html::hiddenInput("SpielerLandWettbewerb[$index][id]", $spiel->id); ?>
                                        <?= SpielerHelper::renderEditableRowMulti($form, $spiel, ['wettbewerb', 'nation', 'position', 'buttons'], 'icon-class', [
                                            'index' => $index,
                                            'positionen' => $positionen,
                                            'nationen' => $nationen,
                                            'tournaments' => $tournaments,
                                        ]); ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-primary mt-2" id="btn-neue-nation" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/new', '_blank')">
                            neue Nation
                        </button>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Wettbewerb</th>
                                    <th>Nation</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= SpielerHelper::renderViewRowMultiNation($laenderspiele, ['tournament', 'nation', 'position'], ['index' => 0]); ?>
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
    
    
</div>
