<?php
use yii\helpers\Html;
use app\components\Helper;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Modal;
use yii\web\JqueryAsset;
$this->registerAssetBundle(JqueryAsset::class);
use yii\bootstrap5\BootstrapAsset;
$this->registerAssetBundle(BootstrapAsset::class);

/** @var array $spiele */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Spiele - " . Html::encode(Helper::getTurniernameFullname($tournamentID));
$this->registerJsFile('@web/js/spiele.js', ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<div class="card">
    <div class="card-header">
        <h3>
            Spiele - <?= Html::encode(Helper::getTurniernameFullname($tournamentID)) ?>
        </h3>
    </div>
    <div class="card-body">
        <table class="table">
             <tbody>
                <?php 
                $letzteRunde = null;
                $letzterTag = null;
                
                foreach ($spiele as $spiel):
                    $aktuelleRunde = $spiel->runde->name ?? 'Unbekannte Runde';
                    
                    // Neue Überschrift bei Wechsel der Runde
                    if ($letzteRunde !== $aktuelleRunde): ?>
                        <tr class="table-primary">
                            <td colspan="6" class="text-left font-weight-bold">
                                <?= Html::encode($aktuelleRunde) ?>
                            </td>
                        </tr>
                        <?php $letzteRunde = $aktuelleRunde;
                    endif;
                    ?>
                    <tr>
                        <td>
                        	<?php $aktuellerTag = Yii::$app->formatter->asDate($spiel->datum, 'php:d.m.Y');?>
                        	<?php if ($letzterTag !== $aktuellerTag):?>
                        		<?=  $aktuellerTag; ?>
                        		<?php $letzterTag = $aktuellerTag;
                        	endif; ?>
                        </td>
                        <td><?= Html::encode(Yii::$app->formatter->asTime($spiel->zeit, 'php:H:i')) ?></td>
                        <td style="text-align: right; width: 30%;"><?= Html::encode($spiel->club1->name ?? 'Unbekannt') ?> <?= Helper::getFlagInfo(Helper::getClubNation($spiel->club1->id), $turnierjahr, false) ?></td>
                        <td style="text-align: center; width: 10%;">
                            <?= Html::a($spiel->getErgebnisHtml(), ['/spielbericht/view', 'id' => $spiel['spielID']], ['class' => 'text-decoration-none']) ?>
                        </td>
                        <td style="width: 50%;"><?= Helper::getFlagInfo(Helper::getClubNation($spiel->club2->id), $turnierjahr, false) ?> <?= Html::encode($spiel->club2->name ?? 'Unbekannt') ?></td>
                        <td>
                            <?php if (!Yii::$app->user->isGuest): ?>
                                <?= Html::button('<i class="fa-regular fa-trash-can"></i>', [
                                    'class' => 'btn btn-danger btn-sm delete-game',
                                    'data-spiel-id' => $spiel->spielID,
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#deleteModal'
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php 
         // Modal-Button
        echo Html::button('Spiel hinzufügen', [
            'class' => 'btn btn-primary',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#add-game-modal',
            'style' => 'background-color: #1C75AC !important;'
        ]);
        
        Modal::begin([
            'id' => 'add-game-modal',
            'title' => 'Spiel hinzufügen',
            'size' => Modal::SIZE_LARGE,
        ]);
        
        // Formular
        $form = ActiveForm::begin([
            'id' => 'add-game-form',
            'action' => ['spiele/create'], // Die Route zum Speichern des Spiels
            'method' => 'post',
            'options' => ['data-pjax' => true],
        ]); ?>
        
        <div class="form-group">
            <?= Html::textInput('club1Text', '', ['id' => 'club1Text', 'class' => 'form-control', 'placeholder' => 'Heimmannschaft suchen...']) ?>
            <?= Html::hiddenInput('club1ID', '', ['id' => 'club1ID']) ?>
        </div>
        
        <div class="form-group">
            <?= Html::textInput('club2Text', '', ['id' => 'club2Text', 'class' => 'form-control', 'placeholder' => 'Auswärtsmannschaft suchen...']) ?>
            <?= Html::hiddenInput('club2ID', '', ['id' => 'club2ID']) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'datum')->input('date') ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'zeit')->input('time') ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'jahr')->textInput() ?>
        </div>
        
        <div class="form-group">
            <?= Html::textInput('wettbewerbText', '', ['id' => 'wettbewerbText', 'class' => 'form-control', 'placeholder' => 'Wettbewerb suchen...']) ?>
            <?= Html::hiddenInput('wettbewerbID', '', ['id' => 'wettbewerbID']) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'gruppe')->dropDownList([
                '' => 'kein',
                '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5',
                'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E',
            ]) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'runde')->dropDownList(array_combine(range(0, 15), range(0, 15))) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'spieltag')->dropDownList(array_combine(range(0, 40), range(0, 40))) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'beschriftung')->textarea() ?>
        </div>
        
        
        <div class="form-group">
            <?= Html::submitButton('Speichern', ['class' => 'btn btn-success', 'style' => 'background-color: #1C75AC !important;']) ?>
        </div>
        
        <?php ActiveForm::end();
        
        Modal::end();
        ?>
        
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Löschen bestätigen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <div class="modal-body">
                        Sind Sie sicher, dass Sie diesen Datensatz löschen möchten?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Löschen</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
