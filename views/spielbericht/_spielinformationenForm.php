<?php
use app\components\Helper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$stadien = \app\models\Stadion::find()->all();
$referees = \app\models\Referee::find()->all();

$stadiumList = implode(', ', array_map(function($s) {
    return Html::encode("{$s->name} ({$s->stadt})") . ' (' . $s->land . ')';
}, $stadien));
    
    $refereeList = implode(', ', array_map(function($r) {
        return Html::encode("{$r->vorname} {$r->name}") . ' (' . $r->nati1 . ')';
    }, $referees));
        ?>

<?php $form = ActiveForm::begin([
    'action' => ['spielbericht/speichern-info'],
    'method' => 'post',
    'options' => ['class' => 'spielinformationen-form']
]) ?>

<div class="spielinfo-box">
    
      <h4><i class="material-icons">fact_check</i> Spieldaten</h4>
<?= Html::hiddenInput('spielID', $spiel->id) ?>

                <!-- Turniername -->
                <?php if ($spiel->turnier->tournament): ?>
                    <div class="info-row">
                        <i class="material-icons">emoji_events</i>
                            <?= Html::input('text', 'namne', Helper::getTurniernameFullname($spiel->turnier->tournament->id, $spiel->turnier->jahr), ['class' => 'form-control', 'readonly' => true, 'disabled' => true]); ?>
                    </div>
                <?php endif; ?>

                <!-- Datum und Zeit -->
                <?php if ($spiel->turnier->datum): ?>
                    <div class="info-row">
                        <i class="material-icons">calendar_month</i>
                        
                        <?= Html::input('text', 'datum', Yii::$app->formatter->asDate($spiel->turnier->datum, 'php:d.m.Y'), [
                            'class' => 'form-control',
                            'readonly' => true,
                            'disabled' => true
                        ]); ?>
                    
                        <?php if ($spiel->turnier->zeit): ?>
                            <?= Html::input('text', 'zeit', Yii::$app->formatter->asTime($spiel->turnier->zeit, 'php:H:i'), [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <!-- Ergebnis -->
<div class="info-row">
  <i class="material-icons">groups</i>
  <div class="input-group" style="width: fit-content; display: flex;">
    <input type="number" name="tore1" class="form-control" value="<?= $spiel->tore1 ?>" style="border-right: 0; width: 60px;">
    <span class="input-group-text" style="border-left: 0; border-right: 0; width: 10px;">:</span>
    <input type="number" name="tore2" class="form-control" value="<?= $spiel->tore2 ?>" style="border-left: 0; border-right: 0; width: 60px;">
    <?= Html::dropDownList('extratimeoptions',
        $spiel->extratime ? 'extratime' : ($spiel->penalty ? 'penalty' : 'regular'),
        [
            'regular' => '--',
            'extratime' => 'n.V.',
            'penalty' => 'i.E.'
        ],
        ['class' => 'form-control', 'style' => 'border-left: 0; min-width: 80px;']
    ) ?>
  </div>
</div>
</div>

<div class="spielinfo-box">
    
      <h4><i class="material-icons">pin_drop</i> Stadion</h4>
    
    <!-- Stadion -->
    <div class="info-row">
        <i class="material-icons">stadium</i>
    <?php
    $stadion = $spiel->stadium;
    $stadionName = $stadion ? "{$stadion->name} ({$stadion->stadt}) ({$stadion->land})" : '';
    $stadionID = $stadion ? $stadion->id : '';
    ?>
    <input type="text" class="autocomplete-input" id="stadiumText"
           data-id-input="stadiumID"
           data-fetch-type="stadium"
           placeholder="Stadion"
           value="<?= Html::encode($stadionName) ?>">
    <input type="hidden" id="stadiumID" name="stadiumID" value="<?= $stadionID ?>">
    <div class="autocomplete-suggestions" id="stadiumText-suggestions"></div>
    
    </div>
    
    <!-- Zuschauer -->
    <div class="info-row">
        <i class="material-icons">groups</i>
        <?= Html::input('number', 'zuschauer', $spiel->zuschauer, ['class' => 'form-control', 'placeholder' => 'Zuschauer']) ?>
    </div>
    
    <div class="info-row">
        <button type="button" class="btn btn-primary mt-2" style="margin-bottom: 5px; font-size: 11px; width: 154px; margin-left: 32px;" id="btn-stadion-bearbeiten" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/stadion/new', '_blank')">
            Neues Stadion
        </button>
    </div>

</div>


<!-- Schiedsrichter -->
<div class="spielinfo-box">

  <h4><i class="material-icons">gavel</i> Schiedsrichter</h4>
    <?php $icons = ['sports', 'sports_score', 'sports_score', 'scoreboard']; ?>
    <?php foreach ([1, 2, 3, 4] as $i): 
        $ref = $spiel["referee{$i}"];
        $refereeName = $ref ? "{$ref->vorname} {$ref->name}" : '';
        $refereeID = $ref ? $ref->id : '';
    ?>
    <div class="info-row">
    	<i class="material-icons"><?= $icons[$i-1]; ?></i>
    
        <input type="text" class="autocomplete-input" id="referee<?= $i; ?>Text"
               data-id-input="referee<?= $i; ?>ID"
               data-fetch-type="referee"
               placeholder="Schiedsrichter <?= $i; ?>"
               value="<?= Html::encode($refereeName) ?>">
        <input type="hidden" id="referee<?= $i; ?>ID" name="referee<?= $i; ?>ID">
        <div class="autocomplete-suggestions" id="referee<?= $i; ?>Text-suggestions"></div>
    </div>
    <?php endforeach; ?>
    
    <div class="info-row">
        <button type="button" class="btn btn-primary mt-2" style="margin-bottom: 5px; font-size: 11px; width: 154px; margin-left: 32px;" id="btn-referee-bearbeiten" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/referee/new', '_blank')">
            Neuer Schiedsrichter
        </button>
    </div>
</div>
    
    <!-- Submit-Button -->
<div class="info-row">
    <?= Html::submitButton('Spielinformationen speichern', ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end() ?>

               