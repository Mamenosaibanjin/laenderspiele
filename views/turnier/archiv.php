<?php
use yii\helpers\Html;
use app\components\Helper;
use app\components\TurnierHelper;
use app\models\Turnier;
use app\components\ClubHelper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Archiv - $turniername";

?>
<div class="verein-page row">
    <!-- Widget 1: Vereinsdaten -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Archiv") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                	<thead>
                		<th>Saison</th>
                		<th>Ergebnisse</th>
                		<th>Mannschaften</th>
                		<th>Sieger</th>
                	</thead>
                    <tbody>
                        <?php foreach ($turniere as $index => $turnier): ?>
                            <tr>
                                <td style="width: 30%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important; text-align: center;">
                                    <b><?= Helper::getTurniernameFullnameForDropdown($turnier['id'])?></b>
                                </td>
                                <td style="width: 20%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('» Ergebnisse', ['/turnier/' . $turnier['id'] . '/spielplan'], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 20%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('» Mannschaften', ['/turnier/' . $turnier['id'] . '/teilnehmer'], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 30%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                	<?php $gewinner = TurnierHelper::getSieger($turnier['id']);?>
                                	<?php 
                                	if ($gewinner) {
                                	   $finale = TurnierHelper::getFinale($turnier['id']);
                                	   echo Html::a(
                                	        Html::img(
                                	            Helper::getClubLogoUrl($gewinner->id),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            ) . Helper::getClubName($gewinner->id), 
                                	        ['/spielbericht/' . $finale->id], 
                                	        ['class' => 'text-decoration-none']
                                	        ); 
                                	}?>
                                	    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
            