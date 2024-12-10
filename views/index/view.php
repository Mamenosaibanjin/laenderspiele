<?php
use yii\helpers\Html;
use app\components\Helper;
use app\models\Club;
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
                    <table class="table">
                        <tr>
                            <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
                                Test
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-palette"></i></th>
                            <td>
                            Test
                            
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div>&nbsp;</div>
            
            <div class="card">
                <div class="card-header"><h3>Nächstes Spiel</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
                                Test
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-palette"></i></th>
                            <td>
                            Test
                            
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div>&nbsp;</div>
            
            <div class="card">
                <div class="card-header"><h3>Letztes Spiel</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
                                Test
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-palette"></i></th>
                            <td>
                            Test
                            
                            </td>
                        </tr>
                    </table>
                </div>
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
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
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
				        <?php foreach ($spieler as $kind): ?>
                        <tr>
                            <td>
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

            <div class="card">
                <div class="card-header"><h3>Fehlende Logos</h3></div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
                                Test
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i></th>
                            <td>Test</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-palette"></i></th>
                            <td>
                            Test
                            
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
    </div>
</div>
