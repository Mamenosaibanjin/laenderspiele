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
    	<h4><i class="material-icons">style</i>Karten</h4>
        <div class="highlights-content">

			<?php foreach ($kartenAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 15%; display: flex; align-items: center; gap: 4px;">
                        <?php if ($aktion->minute > 0) : ?>
                            <span class="material-icons" style="font-size: 16px; color: #666;">schedule</span>
                            <small><?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . "'" : '' ?></small>
                        <?php endif; ?>    
                    </div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 75% !important;"><?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?></div>
                </div>
            <?php endforeach; ?>                
        
        </div>
    </div>
</div>
</div>
