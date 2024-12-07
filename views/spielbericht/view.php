<?php
use app\components\Helper;
use yii\helpers\Html;
use app\models\Spielbericht;

/* @var $this yii\web\View */
/* @var $spiel app\models\Spiel */
/* @var $highlightAktionen app\models\Games[] */
/* @var $aufstellung1 app\models\Aufstellung */
/* @var $aufstellung2 app\models\Aufstellung */

$this->title = 'Spielbericht: ' . Html::encode($spiel->heimClub->name) . ' vs ' . Html::encode($spiel->auswaertsClub->name);
?>

<div class="panel panel-default">
    <div class="panel-heading text-center">
        <div class="row">
            <div class="col-sm-4 text-center">
                <?= Html::img(Helper::getClubLogoUrl($spiel->heimClub->id), ['alt' => $spiel->heimClub->name, 'class' => 'team-logo', 'style' => 'height: 100px;']) ?>
                <div><?= Html::encode($spiel->heimClub->name) ?></div>
            </div>
            <?php //echo "<pre>";var_dump($spiel);echo "</pre>";exit;?>
            <div class="col-sm-4 digital-scoreboard" style="font-size: 50px;">
				<?= $spiel->turnier ? Html::encode($spiel->turnier->getErgebnis()) : 'Kein Ergebnis verfügbar' ?>
                <?php if ($spiel->extratime): ?>
                    <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">n.V.</div>
                <?php elseif ($spiel->penalty): ?>
                    <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">i.E.</div>
                <?php endif; ?>
            </div>
            <div class="col-sm-4 text-center">
                <?= Html::img(Helper::getClubLogoUrl($spiel->auswaertsClub->id), ['alt' => $spiel->auswaertsClub->name, 'class' => 'team-logo', 'style' => 'height: 100px;']) ?>
                <div><?= Html::encode($spiel->auswaertsClub->name) ?></div>
            </div>
        </div>
    </div>
    <div class="panel-body" style="padding-top: 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Highlights
                </span>
            </div>
            <div class="highlights-content">
            <?php foreach ($highlightAktionen as $aktion): ?>
                <div class="highlight-row">
	                <div class="heimname"><?php Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name);?>
	                	<?= $spiel->isHeimAktion($aktion->spieler->id) ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : ' ' ?>
	                </div>
	                <div class="heim">
	                   	<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m') :?>
	                		<?= $spiel->isHeimAktion($aktion->spieler->id) ? Html::encode($aktion->zusatz) : ' ' ?>
	                	<?php endif; ?>
	                </div>
	                <div class="heim">
	                	<?php if ($spiel->isHeimAktion($aktion->spieler->id)) : ?>
           	            	<?= Helper::getActionSvg($aktion->aktion); ?>
	                	<?php endif;?>
	                </div>
                    <div class="minute">
                    	<?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . '.' : ' ' ?>
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
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="panel-body" style="padding-top: 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Spielinformationen
                </span>
            </div>
            <div class="highlights-content">
				<div class="spiel-info" style="text-align: left;">
                <!-- Datum und Zeit -->
                <?php if ($spiel->turnier->datum): ?>
                    <div class="info-row">
	                    <i class="material-icons">calendar_month</i>
                        <span>
                            <?= Yii::$app->formatter->asDate($spiel->turnier->datum, 'php:d.m.Y') ?>
							<?php if ($spiel->turnier->zeit): ?> - <?= Yii::$app->formatter->asTime($spiel->turnier->zeit, 'php:H:i') ?>
							<?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Stadion -->
                <?php if ($spiel->stadium): ?>
                    <div class="info-row">
	                    <i class="material-icons">stadium</i>
                        <span>
                            <img src="<?= Helper::getFlagUrl($spiel->stadium->land) ?>" alt="Flagge" class="flag" style="margin-right: 5px;">
                            <?= Html::encode($spiel->stadium->name) ?> (<?= Html::encode($spiel->stadium->stadt) ?>)
                        </span>
                    </div>
                    <?php if ($spiel->zuschauer): ?>
                        <div class="info-row">
	                    <i class="material-icons">groups</i>
                            <span><?= number_format($spiel->zuschauer, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Schiedsrichter -->
                <?php if ($spiel->referee1): ?>
                    <div class="info-row">
                    <i class="material-icons">sports</i>
                   	<? //Html::img(Yii::getAlias('@web/assets/img/spielbericht/whistle.png'), ['alt' = 'Datum', 'style' = 'height: 25px;'])  Alternative als PNG ?>
                        <span>
                            <img src="<?= Helper::getFlagUrl($spiel->referee1->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px;">
                            <?= Html::encode($spiel->referee1->vorname . ' ' . $spiel->referee1->name) ?>
                        </span>
                    </div>
                    <?php if ($spiel->referee2): ?>
                        <div class="info-row">
	                    <i class="material-icons material-icons_logo">sports_score</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee2->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px;">
                                <?= Html::encode($spiel->referee2->vorname . ' ' . $spiel->referee2->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($spiel->referee3): ?>
                        <div class="info-row">
	                    <i class="material-icons">sports_score</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee3->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px;">
                                <?= Html::encode($spiel->referee3->vorname . ' ' . $spiel->referee3->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($spiel->referee4): ?>
                        <div class="info-row">
	                    <i class="material-icons">scoreboard</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee4->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px;">
                                <?= Html::encode($spiel->referee4->vorname . ' ' . $spiel->referee4->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>            
            </div>
        </div>
    </div>
</div>
            <?php //echo "<pre>";var_dump($highlightAktionen);echo "</pre>";die;?>

<div class="row">
    <div class="col-sm-6">
        <?= $this->render('_widget_tore', ['highlightAktionen' => $highlightAktionen]) ?>
        <?= $this->render('_widget_karten', ['highlightAktionen' => $highlightAktionen]) ?>
    </div>
    <div class="col-sm-6">
        <?= $this->render('_widget_wechsel', ['highlightAktionen' => $highlightAktionen]) ?>
        <?= $this->render('_widget_besondere', ['highlightAktionen' => $highlightAktionen]) ?>
    </div>
</div>
