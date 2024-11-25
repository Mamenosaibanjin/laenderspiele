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
    <h1>
    	<?php if ($spieler->vorname): ?>
    		<?= Html::encode($spieler->vorname);?>
    	<?php endif;?>
		<?= Html::encode($spieler->name); ?>
    </h1>

<div style="height: 50px;">&nbsp;</div>

<!-- Widget: Spielerdaten -->
<div class="container">
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="title">Spielerdaten</h4>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <tbody>
                            <!-- Vorname -->
                            <?php if ($spieler->vorname): ?>
                                <tr>
                                    <td><strong>Vorname:</strong></td>
                                    <td><?= Html::encode($spieler->vorname) ?></td>
                                </tr>
                            <?php endif; ?>
                            <!-- Nachname -->
                            <?php if ($spieler->name): ?>
                                <tr>
                                    <td><strong>Nachname:</strong></td>
                                    <td><?= Html::encode($spieler->name) ?></td>
                                </tr>
                            <?php endif; ?>
                            <!-- Vollständiger Name -->
                            <?php if ($spieler->fullname): ?>
                                <tr>
                                    <td><strong>Vollständiger Name:</strong></td>
                                    <td><?= Html::encode(($spieler->fullname ?? '')) ?></td>
                                </tr>
                            <?php endif; ?>
                            <!-- Geburtstag -->
                            <?php if ($spieler->geburtstag): ?>
                                <tr>
                                    <td><strong>Geboren am:</strong></td>
                                    <td><?= Yii::$app->formatter->asDate($spieler->geburtstag, 'dd.MM.yyyy') ?></td>
                                </tr>
                            <?php endif; ?>
                            <!-- Geburtsort und Geburtsland -->
                            <?php if ($spieler->geburtsort || $spieler->geburtsland): ?>
                                <tr>
                                    <td><strong>Geburtsort:</strong></td>
                                    <td>
                                    	<div class="flag_icon">
                                            <?= Html::encode($spieler->geburtsort) ?>
                                            <?php if ($spieler->geburtsland): ?>
                                                <img src="<?= Html::encode(Helper::getFlagUrl($spieler->geburtsland, $spieler->geburtstag)) ?>" 
                                                     alt="<?= Html::encode($spieler->geburtsland) ?>" 
                                                     style="width: 25px; height: 20px;">
                                            <?php endif; ?>
                                         </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Nationalität -->
                            <?php if ($spieler->nati1 || $spieler->nati2 || $spieler->nati3): ?>
                                <tr>
                                    <td><strong>Nationalität:</strong></td>
                                    <td>
                                    	<div class="flag_icon">
                                            <?php if ($spieler->nati1): ?>
                                                <img src="<?= Html::encode(Helper::getFlagUrl($spieler->nati1)) ?>" 
                                                     alt="<?= Html::encode($spieler->nati1) ?>" 
                                                     style="width: 25px; height: 20px;">
                                            <?php endif; ?>
                                            <?php if ($spieler->nati2): ?>
                                                <img src="<?= Html::encode(Helper::getFlagUrl($spieler->nati2)) ?>" 
                                                     alt="<?= Html::encode($spieler->nati2) ?>" 
                                                     style="width: 25px; height: 20px;">
                                            <?php endif; ?>
                                            <?php if ($spieler->nati3): ?>
                                                <img src="<?= Html::encode(Helper::getFlagUrl($spieler->nati3)) ?>" 
                                                     alt="<?= Html::encode($spieler->nati3) ?>" 
                                                     style="width:25px; height: 20px;">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Größe -->
                            <?php if ($spieler->height): ?>
                                <tr>
                                    <td><strong>Größe:</strong></td>
                                    <td><?= Html::encode($spieler->height) ?> m</td>
                                </tr>
                            <?php endif; ?>
                            <!-- Gewicht -->
                            <?php if ($spieler->weight): ?>
                                <tr>
                                    <td><strong>Gewicht:</strong></td>
                                    <td><?= Html::encode($spieler->weight) ?> kg</td>
                                </tr>
                            <?php endif; ?>
                            <!-- Spielfuß -->
                            <?php if ($spieler->spielfuss): ?>
                                <tr>
                                    <td><strong>Spielfuß:</strong></td>
                                    <td><?= Html::encode($spieler->spielfuss) ?></td>
                                </tr>
                            <?php endif; ?>
                            <!-- Homepage -->
                            <?php if ($spieler->homepage): ?>
                                <tr>
                                    <td><strong>Homepage:</strong></td>
                                    <td>
                                        <?= Html::a('<i class="fa fa-laptop-code"></i>', 'http://' . $spieler->homepage, ['target' => '_blank', 'title' => 'Homepage']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Facebook -->
                            <?php if ($spieler->facebook): ?>
                                <tr>
                                    <td><strong>Facebook:</strong></td>
                                    <td>
										<?= Html::a('<i class="fab fa-facebook"></i>', 'http://www.facebook.com/' . $spieler->facebook, ['target' => '_blank', 'title' => 'Facebook']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Instagram -->
                            <?php if ($spieler->instagram): ?>
                                <tr>
                                    <td><strong>Instagram:</strong></td>
                                    <td>
                                        <?= Html::a('<i class="fab fa-instagram"></i>', 'http://www.instagram.com/' . $spieler->instagram, ['target' => '_blank', 'title' => 'Instagram']) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <!-- Optional Footer -->
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height: 50px;">&nbsp;</div>

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
	                                            <img src="<?= Html::encode(Helper::getFlagUrl($karriere->verein->land, $karriere->von)) ?>" alt="<?= Html::encode($karriere->verein->land) ?>" style="width: 25px; height: 20px;">
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
    	                                            <img src="<?= Html::encode(Helper::getFlagUrl($jugend->verein->land, $jugend->von)) ?>" alt="<?= Html::encode($jugend->verein->land) ?>" style="width: 25px; height: 20px;">
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
                                                         style="width: 30px; height: 30px;">
                                                    <?= Html::encode(Helper::getClubName($spiel->landID) ?? 'Unbekannt') ?>
                                                </div>
                                            </td>
    
                                            <!-- Flagge -->
                                            <td>
                                            	<div class="flag_icon">
                                              	  <img src="<?= Html::encode(Helper::getFlagUrl(Html::encode(Helper::getClubNation($spiel->landID)), $spiel->jahr)) ?>" 
                                                	     alt="<?= Html::encode($spiel->landID) ?>" 
                                                    	 style="width: 25px; height: 20px;">
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
