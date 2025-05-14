<?php
use yii\helpers\Html;
use app\components\Helper;
use app\components\SpielerHelper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Spieler - $turniername $jahr";

?>
<div class="verein-page row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Spieler") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    	<tr>
                    		<th>Spieler</th>
                    		<th colspan="2">Mannschaft</th>
                    		<th>geboren</th>
                    		<th>Größe</th>
                    		<th>Position</th>
                    	</tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spieler as $index => $player): ?>
                            <tr>
                                <td style="width: 35%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a(Helper::getSpielerName($player['spielerID']), ['/spieler/view', 'id' => $player['spielerID']], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 5%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::img(Helper::getClubLogoUrl(SpielerHelper::getNationId($player['spielerID'], $tournamentID)), ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']) ?>
                                </td>
                                <td style="width: 15%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Helper::getClubName(SpielerHelper::getNationId($player['spielerID'], $tournamentID))?>
                                </td>
                                <td style="width: 15%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= SpielerHelper::getBirthday($player['spielerID']) ?>
                                </td>
                                <td style="width: 15%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= SpielerHelper::getHeight($player['spielerID']) ?>
                                </td>
                                <td style="width: 15%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= SpielerHelper::getPosition($player['spielerID'], $tournamentID) ?>
                                </td>
                            </tr>
                            
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
            