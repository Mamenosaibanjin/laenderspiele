<?php
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

                <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                    	<?php 
                    	   $playerID = Yii::$app->request->get('id'); // 51574 in deinem Beispiel
                    	   echo Html::hiddenInput('playerID', $playerID);
                    	?>
                        <table class="table">
                            <?php foreach ($fields as $field): ?>
                                <?= SpielerHelper::renderEditableRow($form, $spieler, $field['attribute'], $field['icon'], $field['options']) ?>
                            <?php endforeach; ?>
                        </table>
                        <div class="form-group">
                            <?= ButtonHelper::saveButton() ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                            <?php foreach ($fields as $field): ?>
                            	<?php if (isset($field)): ?>
                                	<?= SpielerHelper::renderViewRow($field['attribute'], $spieler, $field['icon']) ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
 
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
                    <?php $form = ActiveForm::begin([
                        'id' => 'career-form',
                        'method' => 'post', // WICHTIG: Muss POST sein
                        'action' => ['spieler/view', 'id' => $spieler->id], // Ziel-Action
                    ]); ?>
                    
                    <table class="table" id="career-table">
                        <thead>
                            <tr>
                                <th colspan="2">Zeitraum</th>
                                <th>Verein</th>
                                <th>Position</th>
                                <?php if ($isEditing): ?>
                                    <th>Aktionen</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vereinsKarriere as $index => $karriere): ?>
    									<?= Html::hiddenInput("SpielerVereinSaison[$index][id]", $karriere->id); ?>
                                        <?= SpielerHelper::renderEditableRowMulti($form, $karriere, ['von', 'bis', 'verein', 'position', 'buttons'], 'icon-class', [
                                            'index' => $index,
                                            'positionen' => $positionen,
                                            'vereine' => $vereine,
                                        ]); ?>
	                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <button type="button" class="btn btn-primary mt-2" id="btn-neuer-verein" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/club/new', '_blank')">
                        neuer Verein
                    </button>

                    <?php ActiveForm::end(); ?>
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
                                	<?php $dataid = $spieler->id . '-' . $jugend->vereinID . '-' . $jugend->von . '-' . $jugend->bis . '-' . $jugend->positionID . '-0';?>
                                    	<tr data-id='<?= $dataid ?>'>	                                        
                                    		<td style="<?= $jugend->von <= $currentMonth && ($jugend->bis >= $currentMonth || $jugend->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
    	                                       	<span class="display-mode">
    	                                       		<?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($jugend->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?>
    	                                       	</span>
                                            	<?php if ($isEditing): ?>
                                            		<input type="month" class="form-control edit-mode w-auto" name="von" id="von" value="<?= substr($jugend->von, 0, 4) . '-' . substr($jugend->von, 4, 2) ?>" style="width: 140px !important;">
                                        			<input type="month" class="form-control edit-mode w-auto" name="bis" id="bis" value="<?= substr($jugend->bis, 0, 4) . '-' . substr($jugend->bis, 4, 2) ?>" style="width: 140px !important;">
                                               	<?php endif; ?>
                                        	</td>
                                        
    		                                <?php if ($jugend->verein): ?>
                                                <td style="<?= $jugend->von <= $currentMonth && ($jugend->bis >= $currentMonth || $jugend->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>width: 35px; text-align: right;">
                                                    <span class="display-mode">
                                                        <?= Html::img(Helper::getClubLogoUrl($jugend->verein->id), ['alt' => $jugend->verein->name, 'style' => 'height: 30px;']) ?>
                                                    </span>
                                                </td>
                                                <td style="<?= $jugend->von <= $currentMonth && ($jugend->bis >= $currentMonth || $jugend->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>text-align: left;">
                                                    <span class="display-mode">
                                                        <?= Html::a(Html::encode($jugend->verein->name), ['/club/view', 'id' => $jugend->verein->id], ['class' => 'text-decoration-none']) ?>
                                                    </span>
                                                    <?php if ($isEditing): ?>
                                                        <input type="text" class="form-control edit-mode" id="verein-input" list="vereine-list" value="<?= Html::encode($jugend->verein->name ?? '') ?>" autocomplete="off" style="width: 175px;">
                                                        <input type="hidden" name="vereinID" id="vereinID" value="<?= Html::encode($jugend->vereinID) ?>">
                                                        
                                                        <datalist id="vereine-list">
                                                            <?php foreach ($vereine as $verein): ?>
                                                                <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                            <?php endforeach; ?>
                                                        </datalist>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="<?= $jugend->von <= $currentMonth && ($jugend->bis >= $currentMonth || $jugend->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>">
                                                    <span class="display-mode">
                                                        <?= Html::img(Helper::getFlagUrl($jugend->verein->land), ['alt' => $jugend->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                                                    </span>
                                                </td>
                                            <?php else: ?>
                                                <td colspan="3">
                                                    <span class="display-mode"></span>
                                                    <?php if ($isEditing): ?>
                                                        <div class="edit-mode" style="display: block;">
                                                            <input type="text" class="form-control" id="verein-input" list="vereine-list" value="" autocomplete="off" style="width: 175px;">
                                                            <input type="hidden" name="vereinID" id="vereinID" value="">
                                                            
                                                            <datalist id="vereine-list">
                                                                <?php foreach ($vereine as $verein): ?>
                                                                    <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                                                                <?php endforeach; ?>
                                                            </datalist>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>

                                        <td style="<?= $jugend->von <= $currentMonth && ($jugend->bis >= $currentMonth || $jugend->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
                                        	<span class="display-mode">
                                        		<?= Html::encode($jugend->position->positionKurz) ?>
                                        	</span>
                                        	<?php if ($isEditing): ?>
	                                        	<select class="form-control edit-mode" name="positionID" id="positionID">
                                                    <?php foreach ($positionen as $position): ?>
                                                        <option value="<?= $position->id ?>" <?= $jugend->positionID == $position->id ? 'selected' : '' ?>><?= Html::encode($position->positionLang_de) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isEditing): ?>
                                            <!-- Bootstrap Switch -->
											<td>
                                            	<div class="btn-group-toggle edit-mode" data-toggle="buttons">
                                                    <label class="btn btn-outline-primary btn-sm">
                                                        <input type="checkbox" name="jugend" id="jugend-switch" autocomplete="off" checked="checked"> Jugend
                                                    </label>
                                                </div>
												<!-- Buttons -->
                                                <button class="btn btn-primary btn-sm edit-button display-mode">Bearbeiten</button>
                                                <button class="btn btn-primary btn-sm save-button edit-mode" id="btn-save-youth">Speichern</button>
                                                <button class="btn btn-secondary btn-sm cancel-button edit-mode">Abbrechen</button>
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
