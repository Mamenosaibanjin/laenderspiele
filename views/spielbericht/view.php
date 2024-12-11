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

<div class="card" style="padding-bottom: 25px;">
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
                <?= Html::img(Helper::getClubLogoUrl($spiel->heimClub->id), ['alt' => $spiel->heimClub->name, 'class' => 'team-logo', 'style' => 'height: 100px; padding: 10px;']) ?>
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
                <?= Html::img(Helper::getClubLogoUrl($spiel->auswaertsClub->id), ['alt' => $spiel->auswaertsClub->name, 'class' => 'team-logo', 'style' => 'height: 100px; padding: 10px;']) ?>
                <div><?= Html::encode($spiel->auswaertsClub->name) ?></div>
            </div>
        </div>
    </div>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Highlights
                </span>
            </div>
            <div class="highlights-content">
			<?php if ($highlightAktionen) : ?>
            <?php
            $previousScore = [0, 0]; // [Heimtore, Auswärtstore]
            
            foreach ($highlightAktionen as $aktion): 
                $minute = $aktion->minute;
                $zusatz = $aktion->zusatz; // Ergebnis, z.B. "3:0"
                $currentScore = explode(':', $zusatz);
                $spielerName = ' ';
                $team = ' ';
            
                if (!$aktion->spieler) {
                    // Unbekannter Spieler
                    if ($aktion->aktion === 'ET') {
                        // Eigentor logik
                        if ($currentScore[0] > $previousScore[0]) {
                            $team = 'auswärts'; // Eigentor Heimteam
                        } else {
                            $team = 'heim'; // Eigentor Auswärtsteam
                        }
                    } else {
                        // Normales Tor (kein Schütze bekannt)
                        if ($currentScore[0] > $previousScore[0]) {
                            $team = 'heim';
                        } elseif ($currentScore[1] > $previousScore[1]) {
                            $team = 'auswärts';
                        }
                    }
                    $spielerName = 'unbekannt';
                } else {
                    // Spieler ist bekannt
                    $team = $spiel->isAuswaertsAktion($aktion->spieler->id) ? 'auswärts' : 'heim';
                    $spielerName = Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name);
                }
            
                $actionSvg = Helper::getActionSvg($aktion->aktion);
            ?>
				<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET' || $aktion->aktion == 'RK') :?>
                    <div class="highlight-row">
                        <div class="heimname">
                            <?= ($team === 'heim') ? $spielerName : ' ' ?>
                        </div>
                        <div class="heim">
                            <?= ($team === 'heim' && ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET' || $aktion->aktion == 'RK')) ? Html::encode($zusatz) : ' ' ?>
                        </div>
                        <div class="heim">
                            <?= ($team === 'heim') ? $actionSvg : ' ' ?>
                        </div>
                        <div class="minute">
                            <?= Html::encode($minute) < 200 ? Html::encode($minute) . '.' : ' ' ?>
                        </div>
                        <div class="auswaerts">
                            <?= ($team === 'auswärts') ? $actionSvg : ' ' ?>
                        </div>
                        <div class="auswaerts">
                            <?= ($team === 'auswärts' && ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET' || $aktion->aktion == 'RK')) ? Html::encode($zusatz) : ' ' ?>
                        </div>
                        <div class="auswaertsname">
                            <?= ($team === 'auswärts') ? $spielerName : ' ' ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                    $previousScore = $currentScore; // Ergebnis aktualisieren
                endforeach; 
                ?>
                <?php else : ?>
                	keine Highlights
                <?php endif; ?>
            </div>
        </div>
    </div>

	<!-- Widget Spielinformationen -->
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Spielinformationen
                </span>
            </div>
            <div class="highlights-content heimname" style="width: 100% !important; text-align: left;">
				<div class="spiel-info" style="text-align: left;">

                <!-- Turniername -->
                <?php if ($spiel->turnier->wettbewerb): ?>
                    <div class="info-row">
                    	<i class="material-icons">emoji_events</i>
                        <span>
                            <?= Helper::getTurniernameFullname($spiel->turnier->wettbewerb->ID, $spiel->turnier->jahr) ?>
                        </span>
                    </div>
                <?php endif; ?>

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
    <?php if ($spiel->aufstellung1 || $spiel->aufstellung2) : ?>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div style="max-width: 640px; margin: auto;">
            <div style="float: left; width: 45%;">
            <div class="highlights-box" style="margin-left: 0; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px; border-bottom-style: dashed;">
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
                            <?= Html::a(Html::encode($spieler->vorname . ' ' . $spieler->name), ['spieler/' . $spieler->id], ['class' => 'text-decoration-none']) ?> 
                            <?php $aktionen = Helper::getActionSymbol($spiel->id, $spieler->id);?> 
                            <?php 
                            if ($aktionen) :
                                foreach ($aktionen AS $aktion) :
                                if (isset($aktion['aktion']) &&
                                    in_array($aktion['aktion'], ['TOR', '11mX', '11m', 'ET', 'RK', 'GRK', 'GK', 'AUS', 'EIN'])) {
                                        
                                        echo Helper::getActionSvg($aktion['aktion']) . '<span style="font-size: 9px; position: relative; top: -5px; left: 2px; padding-right: 5px;">' . $aktion['minute'] . '\'</span>';
                                    }
                                    
                                endforeach;
                            endif;
                            ?>
                            <br>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    
                </div>
            </div>
            <?php if ($wechselHeim) : ?>
                <div class="highlights-box" style="margin-left: 0px; padding: 10p; border-radius: 0px; border-top-style: dashed;">
    				<div class="highlights-content heimname" style="text-align: left; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
                    	<?php foreach ($wechselHeim as $aktion): ?>
                    	<?php if ($aktion->spieler2) : ?>
        	                <?= Html::encode(($aktion->spieler2->vorname ? $aktion->spieler2->vorname : '') . ' '  . $aktion->spieler2->name)?>
        	                <?php $aktionen = Helper::getActionSymbol($spiel->id, $aktion->spieler2->id); ?>
        	                <?php 
                                if ($aktionen) :
                                foreach ($aktionen AS $aktion) :
                                        if (isset($aktion['aktion']) &&
                                            in_array($aktion['aktion'], ['TOR', '11mX', '11m', 'ET', 'RK', 'GRK', 'GK', 'AUS', 'EIN'])) {
                                                
                                                echo Helper::getActionSvg($aktion['aktion']) . '<span style="font-size: 9px; position: relative; top: -5px; left: 2px; padding-right: 5px;">' . $aktion['minute'] . '\'</span>';
                                            }
                                    endforeach;
                                endif;
                                ?>
                                <br>
                        <?php else: 
                            echo "unbekannt";
                            echo Helper::getActionSvg('EIN') . '<span style="font-size: 9px; position: relative; top: -5px; left: 2px; padding-right: 5px;">' . $aktion['minute'] . '\'</span>';
                            echo "<br>";
                        ?>
    					<?php endif; ?>
                    	<?php endforeach; ?>
    				</div>            
            	</div>
            <?php endif; ?>
        	<div class="highlights-box" style="margin-left: 0px; padding: 10px 20px 0 20px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
				<div class="highlights-content heimname" style="text-align: left; width: 100% !important; font-weight: bold;">
                	<?php if ($trainer = $spiel->aufstellung1->coach ?? null): ?>
            	        <p>Trainer: <?= Html::encode($trainer->vorname . ' ' . $trainer->name) ?></p>
                	<?php endif; ?>
				</div>            
        	</div>
        	</div><div style="float: left; width: 10%;">&nbsp;
        	</div><div style="float: left; width: 45%;">
        	<div class="highlights-box" style="margin-right: 0px; padding-right: 20px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
        
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
                            <?php $aktionen = Helper::getActionSymbol($spiel->id, $spieler->id);?> 
                            <?php
                            if ($aktionen) :
                                foreach ($aktionen AS $aktion) :
                                
                                if (isset($aktion['aktion']) &&
                                    in_array($aktion['aktion'], ['TOR', '11mX', '11m', 'ET', 'RK', 'GRK', 'GK', 'AUS'])) {
                                        
                                        echo '<span style="font-size: 9px; position: relative; top: -5px; left: -2px; padding-left: 5px;">' . $aktion['minute'] . '\'</span>' . Helper::getActionSvg($aktion['aktion']);
                                    }
                            
                                endforeach;
                            endif;
                            ?>
                            <?= Html::encode($spieler->vorname . ' ' . $spieler->name) ?>
                            <br>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
        	</div>
            <div class="highlights-box" style="margin-left: 0px; padding: 10p; border-radius: 0px; border-top-style: dashed;">
				<div class="highlights-content heimname" style="text-align: right; line-height: 2.3; padding: 8px 0 0 13px; width: 100% !important;">
                	<?php foreach ($wechselAuswaerts as $aktion): ?>
                		<?php if ($aktion->spieler2) :?>
    	                <?php $aktionen = Helper::getActionSymbol($spiel->id, $aktion->spieler2->id); ?>
    	                <?php 
                            if ($aktionen) :
                                if (isset($aktion['aktion']) && in_array($aktion['aktion'], ['TOR', '11mX', '11m', 'ET', 'RK', 'GRK', 'GK', 'AUS', 'EIN'])) {
                                    echo '<span style="font-size: 9px; position: relative; top: -5px; left: -2px; padding-left: 5px;">' . $aktion['minute'] . '\'</span>' . Helper::getActionSvg($aktion['aktion']);
                                }
                            endif;
                            ?>
    	                <?= Html::encode(($aktion->spieler2->vorname ? $aktion->spieler2->vorname : '') . ' '  . $aktion->spieler2->name)?>
                            <br>
                        <?php else: 
                            echo "unbekannt";
                            echo Helper::getActionSvg('EIN') . '<span style="font-size: 9px; position: relative; top: -5px; left: 2px; padding-right: 5px;">' . $aktion['minute'] . '\'</span>';
                            echo "<br>";
                        ?>
                        <?php endif; ?>
                	<?php endforeach; ?>
				</div>            
        	</div>
        	<div class="highlights-box" style="margin-left: 0px; padding: 10px 20px 0 20px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
				<div class="highlights-content auswaertsname" style="text-align: right; width: 100% !important; font-weight: bold;">
                	<?php if ($trainer = $spiel->aufstellung2->coach ?? null): ?>
            	        <p>Trainer: <?= Html::encode($trainer->vorname . ' ' . $trainer->name) ?></p>
                	<?php endif; ?>
				</div>            
        	</div>
        	</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tore-Widget -->
    <?php if ($toreAktionen) :?>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Tore
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($toreAktionen as $aktion): ?>
    			<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET') :?>
                    <div class="highlight-row" <?= Html::encode($aktion->minute) == 201 ? 'style="border-top: 1px dashed black; font-size: 12px; font-weight: bolder;"' : ' ' ?>>
                    	<?= Html::encode($aktion->minute) == 201 ? 'Elfmeterschießen</div><div class="highlight-row">' : ' ' ?>
                        <div class="minute" style="width: 10%;">
                        	<?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) : ' ' ?>
                        </div>
    	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
    	                <div class="auswaerts" style="width: 10%;"><?= Html::encode($aktion->zusatz); ?></div>
    	                <div class="auswaertsname" style="width: 70% !important;"><?= ($aktion->spieler ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : 'unbekannt')?>
    	                <?php if ($aktion->aktion == '11mX') :?>
    	                	verschießt
    	                <?php endif; ?>
    	                </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            </div>
        </div>
    </div>
    <?php endif; ?>
        
    <!-- Besondere Vorkommnisse-Widget -->
    <?php if ($besondereAktionen) :?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Besondere Vorkommnisse
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($besondereAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 80% !important;">
	                <?php if ($aktion->zusatz == 'v') :?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> verschießt Elfmeter
	         		<?php elseif ($aktion->zusatz == 'p') : ?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> schießt Elfmeter an den Pfosten
	         		<?php elseif ($aktion->zusatz == 'l') : ?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> schießt Elfmeter an die Latte
                    <?php elseif ($aktion->zusatz == 'h') : ?>
                        <?php 
                            $gegnerTorhueterID = $spiel->getGegnerTorhueter($aktion->spieler->id); 
                            $gegnerTorhueter = $gegnerTorhueterID ? $spiel->getSpieler($gegnerTorhueterID) : null;
                        ?>
                        <?php if ($gegnerTorhueter) : ?>
                            <?= Html::encode(($gegnerTorhueter->vorname ? mb_substr($gegnerTorhueter->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $gegnerTorhueter->name) ?> hält Elfmeter von <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name) ?>
                        <?php else : ?>
                            Torhüter der gegnerischen Mannschaft hält Elfmeter von <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name) ?>
                        <?php endif; ?>
                    <?php endif; ?>
	                </div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Karten-Widget -->
    <?php if ($kartenAktionen) :?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Karten
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($kartenAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 80% !important;"><?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?></div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
    <?php endif; ?>

	<!-- Wechsel-Widget -->
	<?php if ($wechselAktionen) : ?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Wechsel
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($wechselAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><i class="fa fa-exchange" aria-hidden="true"></i></div>
	                <div class="auswaertsname" style="width: 80% !important; line-height: 19px;">
	                <?php if ($aktion->spieler) : ?>
	                <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?><br>
	                <?php else : ?>
	                	unbekannt<br>
	               	<?php endif; ?>
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
    <?php endif; ?>
</div>
