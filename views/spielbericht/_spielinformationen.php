<?php
use yii\helpers\Html;
use app\components\Helper;

/* @var $spiel app\models\Spiel */
/* @var $heim boolean */
/* @var $stadien app\models\Stadion[] */
/* @var $referees app\models\Referee[] */

$heim = $heim ?? true;
$stadien = $stadien ?? \app\models\Stadion::find()->all();
$referees = $referees ?? \app\models\Referee::find()->all();

$stadiumList = implode(', ', array_map(function($s) {
    return Html::encode("{$s->name} ({$s->stadt})") . ' (' . $s->land . ')';
}, $stadien));
    
    $refereeList = implode(', ', array_map(function($r) {
        return Html::encode("{$r->vorname} {$r->name}") . ' (' . $r->nati1 . ')';
    }, $referees));
        ?>

<div class="panel-body" style="padding: 25px 25px 0 25px;">
	<div class="widget-column">
	<div class="spielinfo-box">
    	<h4><i class="material-icons">info</i>Spielinformationen</h4>
        <div class="highlights-content heimname" style="width: 100% !important; text-align: left;">
            <div class="spiel-info" style="text-align: left;">
                
                <!-- Eingabeformular (nur für eingeloggte Nutzer) -->
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?= $this->render('_spielinformationenForm', compact('spiel', 'stadiumList', 'refereeList')) ?>
                <?php else: ?>
                    <?= $this->render('_spielinformationenReadonly', compact('spiel')) ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
</div>