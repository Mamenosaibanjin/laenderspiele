<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\ButtonHelper;
use app\components\Helper;
use app\components\SpielerHelper;
use app\models\Referee;
use app\components\RefereeHelper;

/** @var $referee app\models\Referee */

?>
<?php
$this->registerJsFile('@web/js/spieler.js',  ['depends' => [\yii\web\JqueryAsset::class]]);

$isEditing = !(Yii::$app->user->isGuest); // Zustand für Bearbeitungsmodus
$this->title = $referee->fullname ?? 'Schiedsrichterprofil';
$fields = [
    ['attribute' => 'vorname', 'icon' => 'fas fa-signature', 'options' => []],
    ['attribute' => 'name', 'icon' => 'fas fa-user', 'options' => []],
    ['attribute' => 'fullname', 'icon' => 'fas fa-address-card', 'options' => []],
    ['attribute' => 'geburtstag', 'icon' => 'fas fa-birthday-cake', 'options' => ['type' => 'date']],
    ['attribute' => 'nati1', 'icon' => 'fas fa-flag', 'options' => ['type' => 'dropdown']],
];
?>


<!-- Spieler-Seite: Header -->
<div class="container">
    <?php $form = ActiveForm::begin([
        'id' => 'referee-form',
        'method' => 'post', // Wichtig: POST-Methode für Formulare
        'action' => ['referee/' . ($referee->id ?: 'new')],
    ]); ?>
    
    <!-- Widget 1: Allgemeine Schiedsrichterdaten -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <?php if ($referee->id == 0): ?>
                            Neuer Schiedsrichter (#<?= Referee::find()->max('id') + 1 ?>)
                        <?php else: ?>                        
                            <?= $isEditing ? "Bearbeiten: {$referee->fullname}" : Html::encode(trim(($referee->vorname ?? '') . ' ' . $referee->name)) ?>
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
                                <?= RefereeHelper::renderEditableRow($form, $referee, $field['attribute'], $field['icon'], $field['options']) ?>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <table class="table">
                            <?php foreach ($fields as $field): ?>
                                <?= RefereeHelper::renderViewRow($field['attribute'], $referee, $field['icon']) ?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
        <div class="form-group">
        <?= ButtonHelper::saveButton() ?>
    </div>
    
    <?php ActiveForm::end(); ?>
    
    
</div>
