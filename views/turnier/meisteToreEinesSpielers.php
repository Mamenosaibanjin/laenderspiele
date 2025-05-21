<?php
use yii\helpers\Html;
use app\components\Helper;
use app\components\SpielerHelper;
use app\components\TurnierHelper;
use app\models\Spiel;
use app\models\Turnier;
use app\components\ClubHelper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Meiste Tore eines Spielers pro Spiel - $turniername";

?>
<div class="verein-page row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Meiste Tore eines Spielers pro Spiel") ?>
                </h3>
            </div>
            <div class="card-body">
            	<?php $anzahlTore = 0; ?>
                <?php foreach ($meisteTore as $t): ?>
                
                    <?php if ($anzahlTore != $t['anzahlTore']) :?>
    					<?php if ($anzahlTore != 0) :?>
                                    </tbody>
	                            </table>
                            </div><br>
                        <?php endif;?>
                        <div class="spielinfo-box">
                        <h4><?= $t['anzahlTore']?> Tore in einem Spiel</h4>
                        <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Spieler</th>
                                        <th>Datum</th>
                                        <th colspan="3" style="text-align: center;">Partie</th>
                                    </tr>
                                </thead>
                            <tbody>
                            <?php endif; ?>
                            <?php         $spiel = Spiel::findOne($t['spielID']);
        
                            ?>
                            <?php
                            switch (true) {
                                case $t['penalty'] == 1:
                                    $ergebnisszusatz = ' i.E.';
                                    break;
                                case $t['extratime'] == 1:
                                    $ergebnisszusatz = ' n.V.';
                                    break;
                                default:
                                    $ergebnisszusatz = '';
                            }
                            ?>
                        
                            <tr>
                                <td width="30%">
	                            	<?= Html::a(Helper::getSpielerName($t['spielerID']), ['/spieler/view', 'id' => $t['spielerID']], ['class' => 'text-decoration-none']) ?>
								</td>
                                <td width="20%"><?= Helper::getFormattedDate($t['datum'])?></td>
                                <td align="right" width="20%">
                                        <?= Helper::getClubName($spiel->club1ID) . " " .  Html::img(
                                        Helper::getClubLogoUrl($spiel->club1ID),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            )?>
                                </td>
                                <td width="10%" style="text-align: center;"><?= Html::a($t['tore1'] . ':' .  $t['tore2'] . $ergebnisszusatz, ['spielbericht/view', 'id' => $t['spielID']], ['class' => 'text-decoration-none']) ?></td>
                                <td width="20%">
                                	<?= Html::img(
                                	    Helper::getClubLogoUrl($spiel->club2ID),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	    ) . Helper::getClubName($spiel->club2ID)?>
                                </td>
                            </tr>
                        	<?php $anzahlTore = $t['anzahlTore']; ?>
						<?php endforeach; ?>    
                        </tbody>
                    </table>
    		        </div><br>
            </div>
        </div>
    </div>

</div>
            