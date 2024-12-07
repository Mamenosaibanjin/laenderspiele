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

<div class="card" style="padding-bottom: 25px";>
	<div class="card-header">
		<h3>Spielbericht <?= Html::encode($spiel->heimClub->name) ?> - <?= Html::encode($spiel->auswaertsClub->name) ?>
		<?= '('. Html::encode($spiel->turnier->getErgebnis()) . ')'?>
		<?php if ($spiel->extratime): ?>
            <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">n.V.</div>
        <?php elseif ($spiel->penalty): ?>
            <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">i.E.</div>
        <?php endif; ?>
		</h3>
	</div>
    <div class="card-body">
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
                <?php echo "Aktion: " . $aktion->aktion . "<br>";
                if ($spiel->isHeimAktion($aktion->spieler->id)) { echo "HeimAktion: " . $spiel->isHeimAktion($aktion->spieler->id) . "<br>"; }
                if ($spiel->isAuswaertsAktion($aktion->spieler->id)) { echo "AuswaertsAktion: " . $spiel->isAuswaertsAktion($aktion->spieler->id) . "<br>"; }?>
                <div class="heimname"><?php Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name);?>
	                	<?= ($aktion != 'ET' && $spiel->isHeimAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id))) ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : ' ' ?>
	                </div>
	                <div class="heim">
	                   	<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m') :?>
	                		<?= ($aktion != 'ET' && $spiel->isHeimAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id))) ? Html::encode($aktion->zusatz) : ' ' ?>
	                	<?php endif; ?>
	                </div>
	                <div class="heim">
	                	<?php if ($aktion != 'ET' && $spiel->isHeimAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id))) : ?>
           	            	<?= Helper::getActionSvg($aktion->aktion); ?>
	                	<?php endif;?>
	                </div>
                    <div class="minute">
                    	<?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . '.' : ' ' ?>
                    </div>
	                <div class="auswaerts">
	                	<?php if ($aktion != 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isHeimAktion($aktion->spieler->id))) : ?>
           	               <?= Helper::getActionSvg($aktion->aktion); ?>
	                	<?php endif;?>
	                </div>
	                <div class="auswaerts">
	                	<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m') :?>
	                		<?= ($aktion != 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isHeimAktion($aktion->spieler->id))) ? Html::encode($aktion->zusatz) : ' ' ?>
	                	<?php endif; ?>
	                </div>
	                <div class="auswaertsname"><?= ($aktion != 'ET' && $spiel->isAuswaertsAktion($aktion->spieler->id) || ($aktion == 'ET' && $spiel->isHeimAktion($aktion->spieler->id))) ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : ' ' ?></div>
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
            <div class="highlights-content heimname" style="width: 100% !important; text-align: left;">
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
                            <img src="<?= Helper::getFlagUrl($spiel->stadium->land) ?>" alt="Flagge" class="flag" style="margin-right: 5px; height: 15px;">
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
                            <img src="<?= Helper::getFlagUrl($spiel->referee1->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px; height: 15px;">
                            <?= Html::encode($spiel->referee1->vorname . ' ' . $spiel->referee1->name) ?>
                        </span>
                    </div>
                    <?php if ($spiel->referee2): ?>
                        <div class="info-row">
	                    <i class="material-icons material-icons_logo">sports_score</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee2->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px; height: 15px;">
                                <?= Html::encode($spiel->referee2->vorname . ' ' . $spiel->referee2->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($spiel->referee3): ?>
                        <div class="info-row">
	                    <i class="material-icons">sports_score</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee3->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px; height: 15px;">
                                <?= Html::encode($spiel->referee3->vorname . ' ' . $spiel->referee3->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($spiel->referee4): ?>
                        <div class="info-row">
	                    <i class="material-icons">scoreboard</i>
                            <span>
                                <img src="<?= Helper::getFlagUrl($spiel->referee4->nati1) ?>" alt="Flagge" class="flag" style="margin-right: 5px; height: 15px;">
                                <?= Html::encode($spiel->referee4->vorname . ' ' . $spiel->referee4->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>            
            </div>
        </div>
    </div>
    
    <!-- Widget Aufstellungen -->
    <div class="panel-body" style="padding-top: 25px;">
        <div style="max-width: 640px; margin: auto;">
        <div class="highlights-box" style="width: 45%; margin-left: 0; float: left;">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Heim
                </span>
            </div>
            <div class="highlights-content heimname" style="text-align: left; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
            
    		<?php foreach (range(1, 11) as $i): ?>
                <?php 
                $spielerProperty = "spieler{$i}";
                $spieler = $spiel->aufstellung1->$spielerProperty ?? null;
                ?>
                <?php if ($spieler): ?>
                    <?= Html::encode($spieler->vorname . ' ' . $spieler->name) ?><br>
                <?php endif; ?>
            <?php endforeach; ?>
        	<?php if ($trainer = $spiel->aufstellung1->coach ?? null): ?>
    	        <p>Trainer: <?= Html::encode($trainer->vorname . ' ' . $trainer->name) ?></p>
        	<?php endif; ?>
                
            </div>
        </div>
        
        <div class="highlights-box" style="width: 45%; margin-right: 0px; padding-right: 20px;">
        
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Auswärts
                </span>
            </div>
                        <div class="highlights-content auswaertsname" style="text-align: right; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
            
    		<?php foreach (range(1, 11) as $i): ?>
                <?php 
                $spielerProperty = "spieler{$i}";
                $spieler = $spiel->aufstellung2->$spielerProperty ?? null;
                ?>
                <?php if ($spieler): ?>
                    <?= Html::encode($spieler->vorname . ' ' . $spieler->name) ?><br>
                <?php endif; ?>
            <?php endforeach; ?>
        	<?php if ($trainer = $spiel->aufstellung2->coach ?? null): ?>
    	        <p>Trainer: <?= Html::encode($trainer->vorname . ' ' . $trainer->name) ?></p>
        	<?php endif; ?>
                
            </div>
        </div>

    <!-- Tore-Widget -->
    <div class="panel-body threerows" style="padding-top: 25px;">
        <div class="highlights-box oneofthree">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Tore
                </span>
            </div>
            <div class="highlights-content heimname" style="text-align: left; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
            
			<?php foreach ($toreAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute">
                    	<?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) . '.' : ' ' ?>
                    </div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaerts" style="width: 10%;">
	                	<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m') :?>
	                		<?= Html::encode($aktion->zusatz); ?>
	                	<?php endif; ?>
	                </div>
	                <div class="auswaertsname" style="width: 70% !important;"><?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?></div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
        
		<!-- Karten-Widget -->
        <div class="highlights-box oneofthree">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Karten
                </span>
            </div>
            <div class="highlights-content heimname" style="text-align: left; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
			<?php foreach ($kartenAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute"><?= Html::encode($aktion->minute)?>
                    </div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 70% !important;"><?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?></div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>

		<!-- Wechsel-Widget -->
        <div class="highlights-box oneofthree">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Wechsel
                </span>
            </div>
            <div class="highlights-content heimname" style="text-align: left; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
			<?php foreach ($wechselAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute"><?= Html::encode($aktion->minute)?>
                    </div>
	                <div class="auswaerts" style="width: 10%;"><i class="fa fa-exchange" aria-hidden="true"></i></div>
	                <div class="auswaertsname" style="width: 70% !important; line-height: 19px;">
	                <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?><br>
	                <?php if ($aktion->spieler2) : ?>
	                	<?= Html::encode(($aktion->spieler2->vorname ? mb_substr($aktion->spieler2->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler2->name)?>
	                <?php else : ?>
	                	unbekannt
	                <?php endif; ?>
	                
	                	                </div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
        
</div>
            <?php //echo "<pre>";var_dump($highlightAktionen);echo "</pre>";die;?>
