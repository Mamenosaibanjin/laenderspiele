<?php
use yii\helpers\Html;
use app\components\Helper;

/* @var $spiel app\models\Spiel */
/* @var $heim boolean */
/* @var $stadien app\models\Stadion[] */
/* @var $referees app\models\Referee[] */
?>
<div class="panel-body" style="padding: 25px 25px 0 25px;">
	<div class="widget-column">
	<div class="spielinfo-box">
    	<h4><i class="material-icons">swap_horiz</i>Wechsel</h4>
        <div class="highlights-content">

			<?php foreach ($wechselAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 15%; display: flex; align-items: center; gap: 4px;">
                        <span class="material-icons" style="font-size: 16px; color: #666;">schedule</span>
                        <small><?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . "'" : '' ?></small>
                    </div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 80% !important; line-height: 19px;">
    	                <?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
	                </div>
                </div>
                <div class="highlight-row">
                    <div class="minute" style="width: 15%; display: flex; align-items: center; gap: 4px;"></div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg('EIN'); ?></div>
	                <div class="auswaertsname" style="width: 80% !important; line-height: 19px;">
    	                <?= ($aktion->spieler2 ? Html::a(Html::encode(trim($aktion->spieler2->vorname . ' ' . $aktion->spieler2->name)), ['/spieler/view', 'id' => $aktion->spieler2->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
	                </div>
                </div>
            <?php endforeach; ?>                
        
        </div>
    </div>
</div>
</div>
