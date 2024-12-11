<?php
use yii\helpers\Html;
use app\components\Helper;
use app\models\Club;
use app\models\Spiel;
use app\models\Spieler;
use app\models\Stadiums;

/* @var $this yii\web\View */
/* @var $club app\models\Club */
/* @var $nation app\models\Nation */
/* @var $stadium app\models\Stadion */
/* @var $recentMatches app\models\Spiel[] */
/* @var $upcomingMatches app\models\Spiel[] */
/* @var $squad app\models\Spieler[] */

?>

<div class="verein-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-12">
        <!-- Widget 1: Vereinsdaten -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>Willkommen</h3></div>
                <div class="card-body">
                    <?= Html::img(\Yii::getAlias('@web/assets/img/index02.jpg'), ['alt' => 'laenderspiele.de', 'style' => 'width: 150px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px; float: left;']); ?>
                    Diese Seite widmet sich ganz den Fußball-Länderspielen – egal ob Männer oder Frauen. Bleib informiert über anstehende Begegnungen, Ergebnisse und mehr!
                    <div class="languages">
                       <?php
                        $words = ['Fußball', 'Soccer', 'Football', 'Fútbol', 'Calcio', 'Futebol', 'Fotboll', 'Футбол', 'Futbol', 'サッカー', '足球', 'كرة القدم'];
                        $positions = []; // Array zur Speicherung der Positionen
                        
                        foreach ($words as $word) {
                            $size = rand(10, 20); // Zufällige Schriftgröße
                            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Zufällige Farbe
                            $width = strlen($word) * $size * 0.6; // Annäherung der Breite basierend auf der Wortlänge
                            $height = $size; // Höhe entspricht der Schriftgröße
                            $overlap = true;
                            
                            while ($overlap) {
                                $top = rand(0, 45);
                                $left = rand(0, 475);
                                
                                $overlap = false;
                                foreach ($positions as $pos) {
                                    // Überprüfen, ob sich die Positionen überschneiden
                                    if (
                                    $left < $pos['left'] + $pos['width'] &&
                                    $left + $width > $pos['left'] &&
                                    $top < $pos['top'] + $pos['height'] &&
                                    $top + $height > $pos['top']
                                    ) {
                                        $overlap = true;
                                        break;
                                    }
                                }
                            }
                            
                            // Position speichern
                            $positions[] = ['top' => $top, 'left' => $left, 'width' => $width, 'height' => $height];
                            
                            // Element ausgeben
                            echo "<span style='position: absolute; top: {$top}px; left: {$left}px; font-size: {$size}px; color: {$color}; font-weight: bold;'>{$word}</span>";
                        }?>
					</div>
                </div>
            </div>
            
            <div>&nbsp;</div>
            
            <?php $todayGamesPlayed = Spiel::getTodayMatches(1)?>
            <?php $lastGame = Spiel::getRecentMatch() ?>
            <?php $todayGamesNotPlayed = Spiel::getTodayMatches(0) ?>
            <?php $nextGame = Spiel::getUpcomingMatch() ?>
            
            <div class="card">
            	<?php if ($todayGamesPlayed) : ?>
                <div class="card-header"><h3>Heutige Ergebnisse</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <?php foreach ($todayGamesPlayed as $index => $match): ?>
                                <tr>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;"><?= Html::encode(Yii::$app->formatter->asTime($match->turnier->zeit, 'php:H:i')) ?></td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;"><?= Html::encode(Helper::getTurniername($match->turnier->wettbewerbID)) ?></td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;"><?= Html::a(Html::encode(Helper::getClubName($match->club1ID) . " - " . Helper::getClubName($match->club2ID)), ['/spielbericht/view', 'id' => $match->id], ['class' => 'text-decoration-none']) ?></td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
										<strong><?= Html::encode($match->tore1) . ':' . Html::encode($match->tore2) ?></strong>
                                        <?php if ($match->extratime): ?> n.V.<?php endif; ?>
                                        <?php if ($match->penalty): ?> i.E.<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>
                <?php else : ?>
                <div class="card-header"><h3>Letztes Spiel (<?= Html::encode(Helper::getTurniername($lastGame[0]->turnier->wettbewerbID) . " " . Yii::$app->formatter->asDate($lastGame[0]->turnier->datum, 'php:d.m.Y')) ?> 
                <?php if ($lastGame[0]->turnier->zeit): ?> - <?= Yii::$app->formatter->asTime($lastGame[0]->turnier->zeit, 'php:H:i') ?><?php endif; ?>)</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 text-center">
                            <?= Html::img(Helper::getClubLogoUrl($lastGame[0]->club1ID), ['alt' => Helper::getClubName($lastGame[0]->club1ID), 'class' => 'team-logo', 'style' => 'height: 50px; padding: 10px;']) ?><br>
                            <?= Html::a(Html::encode(Helper::getClubName($lastGame[0]->club1ID)), ['/club/view', 'id' => $lastGame[0]->club1ID], ['class' => 'text-decoration-none']) ?>
                        </div>
                        <?php //echo "<pre>";var_dump($spiel);echo "</pre>";exit;?>
                        <div class="col-sm-3 digital-scoreboard" style="font-size: 20px; width: 20%;">
            				<?= $lastGame[0]->tore1 . " : " . $lastGame[0]->tore2 ?>
                            <?php if ($lastGame[0]->extratime): ?>
                                <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">n.V.</div>
                            <?php elseif ($lastGame[0]->penalty): ?>
                                <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">i.E.</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <?= Html::img(Helper::getClubLogoUrl($lastGame[0]->club2ID), ['alt' => Helper::getClubName($lastGame[0]->club2ID), 'class' => 'team-logo', 'style' => 'height: 50px; padding: 10px;']) ?><br>
                            <?= Html::a(Html::encode(Helper::getClubName($lastGame[0]->club2ID)), ['/club/view', 'id' => $lastGame[0]->club2ID], ['class' => 'text-decoration-none']) ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div style="padding: 20px 10px;"><?= Html::a("Spielbericht", ['/spielbericht/view', 'id' => $lastGame[0]->id], ['class' => 'text-decoration-none']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
            
            <div>&nbsp;</div>
            
			<div class="card">
            	<?php if ($todayGamesNotPlayed) : ?>
                <div class="card-header"><h3>Heutige Spiele</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <?php foreach ($todayGamesNotPlayed as $index => $match): ?>
                                <tr>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;"><?= Html::encode(Yii::$app->formatter->asTime($match->turnier->zeit, 'php:H:i')) ?></td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;"><?= Html::encode(Helper::getTurniername($match->turnier->wettbewerbID)) ?></td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                        <?= Html::a(
                                            Html::encode(Helper::getClubName($match->club1ID)), 
                                            ['/club/view', 'id' => $match->club1ID], 
                                            ['class' => 'text-decoration-none']
                                        ) 
                                        . " - " . 
                                        Html::a(
                                            Html::encode(Helper::getClubName($match->club2ID)), 
                                            ['/club/view', 'id' => $match->club2ID], 
                                            ['class' => 'text-decoration-none']
                                        ) ?>
									</td>
                                    <td style="background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">&nbsp;</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            	</div>
                <?php elseif ($nextGame) :?>
                    <div class="card-header"><h3>Nächstes Spiel (<?= Html::encode(Helper::getTurniername($nextGame[0]->turnier->wettbewerbID) . " " . Yii::$app->formatter->asDate($nextGame[0]->turnier->datum, 'php:d.m.Y')) ?> 
                    <?php if ($nextGame[0]->turnier->zeit): ?> - <?= Yii::$app->formatter->asTime($nextGame[0]->turnier->zeit, 'php:H:i') ?><?php endif; ?>)</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3 text-center">
                                    <?= Html::img(Helper::getClubLogoUrl($nextGame[0]->club1ID), ['alt' => Helper::getClubName($nextGame[0]->club1ID), 'class' => 'team-logo', 'style' => 'height: 50px; padding: 10px;']) ?><br>
                                    <?= Html::a(Html::encode(Helper::getClubName($nextGame[0]->club1ID)), ['/club/view', 'id' => $nextGame[0]->club1ID], ['class' => 'text-decoration-none']) ?>
                                </div>
                                <?php //echo "<pre>";var_dump($spiel);echo "</pre>";exit;?>
                                <div class="col-sm-3 digital-scoreboard" style="font-size: 20px; width: 20%;">
                    				<?= $nextGame[0]->tore1 . " : " . $nextGame[0]->tore2 ?>
                                    <?php if ($nextGame[0]->extratime): ?>
                                        <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">n.V.</div>
                                    <?php elseif ($nextGame[0]->penalty): ?>
                                        <div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">i.E.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <?= Html::img(Helper::getClubLogoUrl($nextGame[0]->club2ID), [
                                        'alt' => Html::encode((string) Helper::getClubName($nextGame[0]->club2ID)), // Sicherstellen, dass ein String übergeben wird
                                        'class' => 'team-logo',
                                        'style' => 'height: 50px; padding: 10px;'
                                    ]) ?><br>
                                    <?= Html::a(Html::encode((string) Helper::getClubName($nextGame[0]->club2ID)), ['/club/view', 'id' => $nextGame[0]->club2ID], ['class' => 'text-decoration-none']) ?>
                                </div>
                                <div class="col-sm-3 text-center">&nbsp;</div>
                            </div>
                        </div>
                	</div>
                <?php else : ?>
	                <div class="card-header"><h3>Nächstes Spiel</h3></div> 
                    <div class="card-body">keine Spiele</div>
                <?php endif;?>
                
            </div>
            
        </div>

           
         <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3>Zufallsauswahl</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                            <td>
                            	<?php $clubId = Club::getZufallsId();
                            	echo "<div style='padding: 5px 0;'>";
                            	echo Html::img(Helper::getFlagUrl(Helper::getClubNation($clubId)), ['alt' => Html::encode(Helper::getClubName($clubId)), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']);
                                echo Html::img(Helper::getClubLogoUrl($clubId), ['alt' => Html::encode(Helper::getClubName($clubId)), 'style' => 'height: 30px; padding-right: 10px;']);
                            	echo Html::a(Html::encode(Helper::getClubName($clubId)), ['/club/view', 'id' => $clubId], ['class' => 'text-decoration-none']);
                            	echo "</div>";
                             ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td>
                            	<?php $stadiumId = Stadiums::getZufallsId();
                            	echo "<div style='padding: 5px 0;'>";
                            	echo Html::img(Helper::getFlagUrl(Helper::getStadionNation($stadiumId)), ['alt' => Html::encode(Helper::getClubName($clubId)), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']);
                            	echo Html::encode(Helper::getStadionName($stadiumId));
                            	echo "</div>";
                             ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="border-bottom: 0; padding-bottom: 0;"><i class="fas fa-earth-europe"></i></th>
                            <td style="border-bottom: 0; padding-bottom: 0;">
                            	<?php $spielerId = Spieler::getZufallsId();
                            	echo "<div style='padding: 5px 0;'>";
                            	echo Html::img(Helper::getFlagUrl(Helper::getSpielerNation($spielerId)), ['alt' => Html::encode(Helper::getSpielerName($spielerId)), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']);
                                echo Html::a(Html::encode(Helper::getSpielerName($spielerId)), ['/spieler/view', 'id' => $spielerId], ['class' => 'text-decoration-none']);
                            	echo "</div>";
                             ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

	         <div>&nbsp;</div>

            <div class="card">
                <div class="card-header"><h3>Geburtstagskinder</h3></div>
                <div class="card-body">
                    <table class="table">
                        <?php $spieler = Spieler::getGeburtstagskinder(date('Y-m-d')); ?>
				        <?php $count = 0;?>
				        <?php foreach ($spieler as $kind): ?>
				        <?php $count++; ?>
                        <tr>
                            <td <?= ($count == 5) ? 'style="border-bottom: 0; padding-bottom: 0;"' : '' ?>>
								<?= Html::img(Helper::getFlagUrl($kind['land']), ['alt' => Html::encode(Helper::getSpielerName($kind['id'])), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']); ?>
                                <?= Html::a(Html::encode(Helper::getSpielerName($kind['id'])), ['/spieler/view', 'id' => $kind['id']], ['class' => 'text-decoration-none']); ?> 
                                (<?= $kind['Age'] ?>)
                            </td>
                        </tr>
            			<?php endforeach; ?>
                    </table>
                </div>
            </div>
     
             <div>&nbsp;</div>

            <div class="card" style="display: none;">
                <div class="card-header"><h3>Fehlende Logos</h3></div>
                <div class="card-body">
                    <table class="table">
                    	<tr>
                    		<td style="border-bottom: 0; padding-bottom: 0;">
                    	<?php
                    	$logos = Club::getFehlendeLogos();
                    	?>
                    	<?php foreach ($logos as $logo):
	                       	echo "<div style='padding: 5px 0;'>";
	                       	echo Html::img(Helper::getFlagUrl($logo->land), ['alt' => Html::encode($logo->name), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']);
	                       	echo Html::encode($logo->name);
                         	echo "</div>";
                         	endforeach;
                         	?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
    </div>
</div>
