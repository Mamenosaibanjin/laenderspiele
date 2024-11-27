<?php
use yii\helpers\Html;
use app\components\Helper;

/* @var $this yii\web\View */
/* @var $club app\models\Club */
/* @var $nation app\models\Nation */
/* @var $stadium app\models\Stadion */
/* @var $recentMatches app\models\Spiel[] */
/* @var $upcomingMatches app\models\Spiel[] */
/* @var $squad app\models\Spieler[] */

$this->title = $club->namevoll;
?>

<div class="verein-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-3">
        <!-- Widget 1: Vereinsdaten -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Vereinsdaten</div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th><i class="fas fa-shield-alt"></i></th>
                            <td><?= Html::encode($club->name) ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td><?= Html::encode($club->namevoll) ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-earth-europe"></i></th>
                            <td>
                                <?= Helper::getFlagUrl($club->land) ? Html::img(Helper::getFlagUrl($club->land), ['alt' => $nation->land_de , 'style' => 'width: 25px; height: 20px; border-radius: 5px;']) : '' ?>
                                <?= Html::encode($nation->land_de) ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i></th>
                            <td><?= Html::encode(DateTime::createFromFormat('Y-m-d', $club->founded)->format('d.m.Y')) ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-palette"></i></th>
                            <td>
                                <?php foreach (explode('-', $club->farben) as $color): ?>
                                    <span style="display:inline-block; width:20px; height:20px; background-color:<?= Html::encode(Helper::colorToHex($color)) ?>; border:1px solid #000;"></span>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-location-dot"></i></th>
                            <td>
                                <?= Html::encode($stadium->name) ?><br>
                                <?= Html::encode($stadium->kapazitaet) ?> Plätze
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope"></i></th>
                            <td>
                                <?= Html::encode($club->name) ?><br>
                                <?= $club->postfach ? 'Postfach ' . Html::encode($club->postfach) . '<br>' : '' ?>
                                <?= $club->strasse ? Html::encode($club->strasse) . '<br>' : '' ?>
                                <?= $club->ort ? Html::encode($club->ort) . '<br>' : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone"></i></th>
                            <td><?= Html::encode($club->telefon) ?></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-laptop-code"></i></th>
                            <td><?= Html::a($club->homepage, $club->homepage, ['target' => '_blank']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Widget 2: Zusammenfassung -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?= Html::encode($club->name) ?></h3>
                    <img src="<?= Html::encode(Helper::getClubLogoUrl($club->id)) ?>" class="img-fluid" alt="<?= Html::encode($club->name) ?>" style="width: 100px; height: 100px;">
                    <p><?= Html::encode($club->namevoll) ?></p>
                    <hr>
                    <div class="row">
                        <div class="col-6">Land:</div>
                        <div class="col-6"><?= Html::encode($nation->land_de) ?></div>
                        <div class="col-6">gegründet:</div>
                        <div class="col-6"><?= Html::encode(DateTime::createFromFormat('Y-m-d', $club->founded)->format('d.m.Y')) ?></div>
                        <div class="col-6">Stadion:</div>
                        <div class="col-6"><?= Html::encode($stadium->name) ?></div>
                        <div class="col-6">Homepage:</div>
                        <div class="col-6"><?= Html::a($club->homepage, $club->homepage, ['target' => '_blank']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<?php if ($recentMatches || $upcomingMatches): ?>
        <!-- Zweite Widgetreihe -->
        <div class="row mb-3">
            <!-- Widget 3: Letzte 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Letzte 5 Spiele</div>
                    <div class="card-body">
                        <?php if ($recentMatches): ?>
                            <ul class="list-group">
                                <?php foreach ($recentMatches as $match): ?>
                                    <li class="list-group-item">
                                        <?= Html::encode($match->datum) ?> - 
                                        <?= Html::encode($match->club1ID == $club->id ? $match->club2->name : $match->club1->name) ?>
                                        (<?= $match->tore1 ?>:<?= $match->tore2 ?> 
                                        <?php if ($match->extratime): ?>n.V.<?php endif; ?>
                                        <?php if ($match->penalty): ?>i.E.<?php endif; ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Keine Spiele gefunden.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    
            <!-- Widget 4: Kommende 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Kommende 5 Spiele</div>
                    <div class="card-body">
                        <?php if ($upcomingMatches): ?>
                            <ul class="list-group">
                                <?php foreach ($upcomingMatches as $match): ?>
                                    <li class="list-group-item">
                                        <?= Html::encode($match->datum) ?> - 
                                        <?= Html::encode($match->club1ID == $club->id ? $match->club2->name : $match->club1->name) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Keine Spiele geplant.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if($squad): 
	
        // Mapping-Array für Positionen
        $positionMapping = [
            1 => 'Tor',
            2 => 'Abwehr',
            3 => 'Mittelfeld',
            4 => 'Sturm',
            5 => 'Trainer',
        ];
        ?>
        <!-- Dritte Widgetreihe -->
        <div class="row">
            <!-- Widget 5: Aktueller Kader -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Aktueller Kader</div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach (['Tor', 'Abwehr', 'Mittelfeld', 'Sturm', 'Trainer'] as $position): ?>
                                <div class="col-md-2">
                                    <h5><?= Html::encode($position) ?></h5>
                                    <ul>
                                        <?php foreach ($squad as $player): ?>
                                		<?php 
                                		// Über die Relation vereinSaison die positionID abrufen
                                		$positionID = $player->vereinSaison[0]->positionID ?? null; 
                                		// Übersetzung der numerischen Position
                                        $playerPositionName = $positionMapping[$positionID] ?? null; 
                                        ?>
                                            <?php if ($playerPositionName == $position): ?>
                                                <li>
                                                    <?= Html::a(Html::encode($player->name . ($player->vorname ? ', ' . $player->vorname[0] . '.' : '')), ['/spieler/view', 'id' => $player->id]) ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-end">
                            <?= Html::a('Detaillierter Kader', ['/kader/view', 'id' => $club->id], ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

	<?php endif; ?>

</div>
