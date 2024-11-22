<?php
use yii\helpers\Html;
use app\components\Helper;

/** @var $spieler app\models\Spieler */
/** @var $vereinsKarriere app\models\SpielerVereinSaison[] */
/** @var $jugendvereine app\models\SpielerVereinSaison[] */
/** @var $laenderspiele app\models\SpielerLandWettbewerb */

$this->title = $spieler->fullname;
?>

<div class="spieler-view">
    <h1><?= Html::encode($spieler->fullname) ?></h1>

    <!-- Widget: Spielerdaten -->
    <div class="widget">
        <h2>Spielerdaten</h2>
        <p><strong>Name:</strong> <?= Html::encode($spieler->vorname . ' ' . $spieler->name) ?></p>
        <p><strong>Geboren am:</strong> <?= Yii::$app->formatter->asDate($spieler->geburtstag) ?></p>
        <p><strong>Geburtsort:</strong> <?= Html::encode($spieler->geburtsort) ?>, 
            <img src="/flags/<?= Html::encode(strtolower($spieler->geburtsland)) ?>.png" alt="<?= Html::encode($spieler->geburtsland) ?>"></p>
        <p><strong>Größe:</strong> <?= Html::encode($spieler->height) ?> m</p>
        <p><strong>Gewicht:</strong> <?= Html::encode($spieler->weight) ?> kg</p>
        <p><strong>Spielfuß:</strong> <?= Html::encode($spieler->spielfuss) ?></p>
        <p>
            <strong>Social Media:</strong>
            <?= $spieler->facebook ? Html::a('Facebook', $spieler->facebook, ['target' => '_blank']) : '' ?>
            <?= $spieler->instagram ? Html::a('Instagram', $spieler->instagram, ['target' => '_blank']) : '' ?>
        </p>
    </div>

 <!-- Widget: Vereins-Karriere -->
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-10">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-sm-5 col-xs-12">
                                <h4 class="title">Vereins-Karriere</h4>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th>Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vereinsKarriere as $karriere): ?>
                                    <tr>
                                        <!-- Zeitraum -->
                                        <td>
                                            <?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->von)->format('Y-m-d'), 'MM/yyyy')) ?> -
                                            <?= Html::encode($karriere->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?>
                                        </td>
    
                                        <!-- Verein -->
                                        <td>
                                            <div class="user_icon">
                                                <img src="<?= Html::encode(Helper::getClubLogoUrl($karriere->verein->ID)) ?>" alt="<?= Html::encode($karriere->verein->name) ?>">
                                            </div>
                                            <?= Html::encode($karriere->verein->name) ?>
                                        </td>
    
                                        <!-- Land -->
                                        <td>
                                            <div class="flag_icon">
	                                            <img src="<?= Html::encode(Helper::getFlagUrl($karriere->verein->land, $karriere->von)) ?>" alt="<?= Html::encode($karriere->verein->land) ?>" style="width: 20px; height: 20px;">
											</div>
                                        </td>
    
                                        <!-- Position -->
                                        <td>
                                            <?= Html::encode($karriere->position->positionKurz) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <!-- Optional Footer: Falls nicht benötigt, kannst du das entfernen -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Widget: Jugendvereine -->
    <div class="widget">
        <h2>Jugendvereine</h2>
        <ul>
            <?php foreach ($jugendvereine as $jugend): ?>
                <li>
                    <?= Html::encode($jugend->verein->name) ?> 
                    <img src="/flags/<?= Html::encode(strtolower($jugend->verein->land)) ?>.png" alt="<?= Html::encode($jugend->verein->land) ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Widget: Länderspiel-Karriere -->
    <div class="widget">
        <h2>Länderspiel-Karriere</h2>
        <table>
            <thead>
                <tr>
                    <th>Wettbewerb</th>
                    <th>Nationalmannschaft</th>
                    <th>Pos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laenderspiele as $spiel): ?>
                    <tr>
                        <td><?= Html::encode($spiel->wettbewerb->name) ?></td>
                        <td>
                            <?= Html::encode(Helper::getFlagUrl($spiel->landID, $spiel->jahr)) ?>
                            <?= Html::encode($spiel->landID) ?>
                        </td>
                        <td><?= Html::encode($spiel->position->positionKurz) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
