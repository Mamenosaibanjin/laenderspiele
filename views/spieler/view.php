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
                                    <th style="width: 150px;">Zeitraum</th>
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
    
    <div style="height: 50px;">&nbsp;</div>

	<?php if (!empty($jugendvereine)): ?>
        <!-- Widget: Jugendvereine -->
        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-10">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col col-sm-5 col-xs-12">
                                    <h4 class="title">Jugendvereine</h4>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 140px;">&nbsp;</th>
                                        <th>Verein</th>
                                        <th>Land</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jugendvereine as $jugend): ?>
                                        <tr>
                                            <!-- kein Zeitraum: leere Spalte -->
                                            <td>
                                            <?= Html::encode($jugend->von ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->von)->format('Y-m-d'), 'yyyy') . ' - ' : '') ?>
                                            <?= Html::encode($jugend->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $jugend->bis)->format('Y-m-d'), 'yyyy') : '') ?>
                                            </td>
        
                                            <!-- Verein -->
                                            <td>
                                                <div class="user_icon">
                                                    <img src="<?= Html::encode(Helper::getClubLogoUrl($jugend->verein->ID)) ?>" alt="<?= Html::encode($jugend->verein->name) ?>">
                                                </div>
                                                <?= Html::encode($jugend->verein->name) ?>
                                            </td>
        
                                            <!-- Land -->
                                            <td>
                                                <div class="flag_icon">
    	                                            <img src="<?= Html::encode(Helper::getFlagUrl($jugend->verein->land, $jugend->von)) ?>" alt="<?= Html::encode($jugend->verein->land) ?>" style="width: 20px; height: 20px;">
    											</div>
                                            </td>
        
                                            <!-- Position -->
                                            <td>
                                                <?= Html::encode($jugend->position->positionKurz) ?>
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
	<?php endif; ?>

    <div style="height: 50px;">&nbsp;</div>

    <?php if (!empty($laenderspiele)): ?>
        <!-- Widget: Länderspiel-Karriere -->
        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-10">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col col-sm-5 col-xs-12">
                                    <h4 class="title">Länderspiel-Karriere</h4>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Wettbewerb</th>
                                        <th>Nation</th>
                                        <th>Flagge</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($laenderspiele as $spiel): ?>
                                        <tr>
                                            <!-- Wettbewerb -->
                                            <td>
                                                <?= Html::encode($spiel->wettbewerb->name) ?> 
                                                <?= Html::encode($spiel->jahr) ?>
                                            </td>
    
                                            <!-- Nation -->
                                            <td>
                                                <div class="nation_icon">
                                                    <img src="<?= Html::encode(Helper::getClubLogoUrl($spiel->landID)) ?>" 
                                                         alt="<?= Html::encode($spiel->landID) ?>" 
                                                         style="width: 20px; height: 20px;">
                                                    <?= Html::encode(Helper::getClubName($spiel->landID) ?? 'Unbekannt') ?>
                                                </div>
                                            </td>
    
                                            <!-- Flagge -->
                                            <td>
                                            	<div class="flag_icon">
                                              	  <img src="<?= Html::encode(Helper::getFlagUrl(Html::encode(Helper::getClubNation($spiel->landID)), $spiel->jahr)) ?>" 
                                                	     alt="<?= Html::encode($spiel->landID) ?>" 
                                                    	 style="width: 20px; height: 20px;">
                                            	</div>
                                            </td>
    
                                            <!-- Position -->
                                            <td><?= Html::encode($spiel->position->positionKurz) ?></td>
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
    <?php endif; ?>
</div>
