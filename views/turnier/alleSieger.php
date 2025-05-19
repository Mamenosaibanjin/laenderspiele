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

$this->title = "Alle Sieger - $turniername";

?>
<div class="verein-page row">
    <!-- Widget 1: Vereinsdaten -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Alle Sieger") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                	<thead>
                		<th>Jahr</th>
                		<th>Sieger</th>
                		<th>Land</th>
                	</thead>
                    <tbody>
                        <?php foreach ($turniere as $index => $turnier): ?>
                            <tr>
                                <td style="width: 20%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important; text-align: right;">
                                    <?= Html::a(Helper::getTurnierJahr($turnier['id']), ['/turnier/' . $turnier['id'] . '/spielplan'], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 40%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
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
                                <td style="width: 40%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                	<?php 
                                	if ($gewinner) {
                                	   $finale = TurnierHelper::getFinale($turnier['id']);
                                	   echo Helper::getFlagInfo($gewinner['land'], Helper::getTurnierStartdatum($turnier['id']), true);
                                	}?>
                                	    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    	<div class="col-md-1">&nbsp;</div>
    
    <!-- Widget 2: Zusammenfassung -->
	<div class="col-md-4">
		<div class="card">
			<div class="card-header">
            <h4>Rekordsieger</h4>
        </div>
        <div class="card-body">
            <table class="table table-sm table-hover">
                <tbody>
                    <?php 
                    $rekordsieger = \app\components\TurnierHelper::getRekordsieger($turnier['id']); 
                    $anzahlSiege = 0;
                    foreach ($rekordsieger as $index => $entry): ?>
                        <tr>
                            <td>
                            	<?php if ($anzahlSiege != $entry['siege']) :?>
	                            	<?= ($index == 0) ? "<b>" : ""; ?>
	                            	<?= $entry['siege'] ?>
	                            	<?= ($index == 0) ? "</b>" : ""; ?>
	                            <?php endif; ?>
	                            <?php $anzahlSiege = $entry['siege'];?>
                            </td>
                            <td>
                                <?= Html::img(
                                    Helper::getClubLogoUrl($entry['clubID']),
                                    ['alt' => 'Logo', 'style' => 'height: 16px; margin-right: 5px;']
                                ) ?>
                                <?= Helper::getClubName($entry['clubID']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>

    </div>
    

</div>
            