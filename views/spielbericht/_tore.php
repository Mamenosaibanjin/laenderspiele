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
			<?php if ($aktion->minute > 199 || $aktion->aktion != '11mX') :?>
                <div class="highlight-row" <?= Html::encode($aktion->minute) == 200 ? 'style="border-top: 1px dashed black; font-size: 12px; font-weight: bolder;"' : ' ' ?>>
                	<?= Html::encode($aktion->minute) == 200 ? 'Elfmeterschießen</div><div class="highlight-row">' : ' ' ?>
                    <div class="minute" style="width: 15%; display: flex; align-items: center; gap: 4px;">
                    	<?php if ($aktion->minute < 200) :?>
                            <span class="material-icons" style="font-size: 16px; color: #666;">schedule</span>
                            <small><?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . "'" : '' ?></small>
                    	<?php endif; ?>
                    </div>
                    <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
                    <div class="auswaerts" style="width: 10%;">
                    <?php if (!($aktion->minute >= 200 && $aktion->aktion == '11mX')) :?>
                    	<b><?= Html::encode($aktion->zusatz); ?></b>
                    <?php endif; ?>
                    </div>
                    <div class="auswaertsname" style="width: 65% !important;">
                    <?php if ($aktion->minute < 200) : ?>
                        <?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
                        <?php if ($aktion->aktion == '11m') :?>
                        	(Elfmeter)
                        <?php elseif ($aktion->aktion == 'ET') :?>
                        	(Eigentor)
                        <?php endif; ?>
                    <?php else :?>
    					<?php if ($aktion->zusatz == 'v') :?>
    	         			<?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?> verschießt Elfmeter
    	         		<?php elseif ($aktion->zusatz == 'p') : ?>
    	         			<?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?> schießt Elfmeter an den Pfosten
    	         		<?php elseif ($aktion->zusatz == 'l') : ?>
    	         			<?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?> schießt Elfmeter an die Latte
                        <?php elseif ($aktion->zusatz == 'h') : ?>
                            <?= ($aktion->spieler2 ? Html::a(Html::encode(trim($aktion->spieler2->vorname . ' ' . $aktion->spieler2->name)), ['/spieler/view', 'id' => $aktion->spieler2->id], ['class' => 'text-decoration-none']) : 'unbekannt')?> hält Elfmeter von <?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
						<?php else :?>
						    <?= ($aktion->spieler ? Html::a(Html::encode(trim($aktion->spieler->vorname . ' ' . $aktion->spieler->name)), ['/spieler/view', 'id' => $aktion->spieler->id], ['class' => 'text-decoration-none']) : 'unbekannt')?>
						<?php endif; ?>
                    <?php endif;?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        </div>
    </div>
</div>
</div>
