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
    	<h4><i class="material-icons">sports_soccer</i>Tore</h4>
        <div class="highlights-content">
		<?php foreach ($toreAktionen as $aktion): ?>
			<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET') :?>
                <div class="highlight-row" <?= Html::encode($aktion->minute) == 201 ? 'style="border-top: 1px dashed black; font-size: 12px; font-weight: bolder;"' : ' ' ?>>
                	<?= Html::encode($aktion->minute) == 201 ? 'Elfmeterschießen</div><div class="highlight-row">' : ' ' ?>
                    <div class="minute" style="width: 15%; display: flex; align-items: center; gap: 4px;">
                        <span class="material-icons" style="font-size: 16px; color: #666;">schedule</span>
                        <small><?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . "'" : '' ?></small>
                    </div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaerts" style="width: 10%;"><b><?= Html::encode($aktion->zusatz); ?></b></div>
	                <div class="auswaertsname" style="width: 65% !important;"><?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
	                <?php if ($aktion->aktion == '11m') :?>
	                	(Elfmeter)
	                <?php elseif ($aktion->aktion == 'ET') :?>
	                	(Eigentor)
	                <?php endif; ?>

	                </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        </div>
    </div>
</div>
</div>
