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

$this->title = "Tore pro Saison - $turniername";

?>
<div class="verein-page row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Tore pro Saison") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
    <thead>
        <tr>
        	<th>#</th>
            <th>Saison</th>
            <th>Tore</th>
            <th>Spiele</th>
            <th>Ø Tore / Spiel</th>
        </tr>
    </thead>
    <tbody>
    	<?php
    	   $counter = 1;
    	   $gesamtTore = 0;
    	   $gesamtSpiele = 0;
    	?>
    	
        <?php foreach ($statistikTore as $t): ?>
            <tr>
            	<td><b><?= $counter; ?></b></td>
                <td>
                    <?= Html::a(Helper::getTurniernameFullnameForDropdown($t['tournamentID']), ['/turnier/' . $t['tournamentID'] . '/spielplan'], ['class' => 'text-decoration-none']) ?>
                </td>
                <td><?= $t['tore'] ?></td>
                <td><?= $t['spiele'] ?></td>
                <td><?= number_format($t['durchschnitt'], 4, ',', '.') ?></td>
            </tr>
            <?php 
                $counter = $counter+1;
                $gesamtTore = $gesamtTore+$t['tore'];
                $gesamtSpiele = $gesamtSpiele+$t['spiele'];
            ?>
        <?php endforeach; ?>
        <tr>
        	<td><b>∑</b></td>
            <td>
                insgesamt <b><?= $counter-1 ?></b>
            </td>
            <td><b><?= $gesamtTore ?></b></td>
            <td><b><?= $gesamtSpiele ?></b></td>
            <td><b>Ø <?= number_format(round($gesamtTore / $gesamtSpiele, 2), 2, ',', '.') ?></b></td>
        </tr>
        
    </tbody>
</table>

            </div>
        </div>
    </div>

</div>
            