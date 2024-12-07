<div class="minute">
<?php use app\components\Helper;
use yii\helpers\Html;

Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . '.' : ' ' ?>
</div>
<div class="auswaerts">
	<?php if ($spiel->isAuswaertsAktion($aktion->spieler->id)) : ?>
   <?= Helper::getActionSvg($aktion->aktion); ?>
<?php endif;?>
</div>
<div class="auswaerts">
	<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m') :?>
	<?= $spiel->isAuswaertsAktion($aktion->spieler->id) ? Html::encode($aktion->zusatz) : ' ' ?>
<?php endif; ?>
</div>
<div class="auswaertsname"><?= $spiel->isAuswaertsAktion($aktion->spieler->id) ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : ' ' ?></div>