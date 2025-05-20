<?php
use yii\helpers\Html;
use app\components\Helper;
use app\components\SpielerHelper;
use app\components\TurnierHelper;
use app\models\Turnier;
use app\components\ClubHelper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Höchste Siege - $turniername";

?>
<div class="verein-page row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Höchste Siege") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Saison</th>
                            <th>Runde</th>
                        	<th>Datum</th>
                            <th colspan="3" style="text-align: center;">Partie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hoechsteSiege as $t): ?>
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
                                <td width="20%"><?= Helper::getFormattedDate($t['datum'])?></td>
                                <td width="20%"><?= Html::a(Helper::getTurniernameFullnameForDropdown($t['tournamentID']), ['/turnier/' . $t['tournamentID'] . '/ergebnisse/8'], ['class' => 'text-decoration-none']) ?></td>
                                <td width="20%"><?= Html::a(Helper::getRundename($t['rundeID']), ['/turnier/' . $t['tournamentID'] . '/ergebnisse/' . $t['rundeID']], ['class' => 'text-decoration-none']) ?></td>
                                <td align="right" width="15%">
                                	<?= Helper::getClubName($t['club1ID']) . " " .  Html::img(
                                	            Helper::getClubLogoUrl($t['club1ID']),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            )?>
                                </td>
                                <td width="10%" style="text-align: center;"><?= Html::a($t['tore1'] . ':' .  $t['tore2'] . $ergebnisszusatz, ['spielbericht/view', 'id' => $t['id']], ['class' => 'text-decoration-none']) ?></td>
                                <td width="15%">
                                	<?= Html::img(
                                	            Helper::getClubLogoUrl($t['club2ID']),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            ) . Helper::getClubName($t['club2ID'])?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>
            