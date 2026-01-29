<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use app\components\Helper;
use app\components\SpielerHelper;
use app\models\SpielerLandWettbewerb;
use yii\helpers\Url;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */
/** @var yii\data\ActiveDataProvider $dataProvider */


$this->title = "Torschützenliste - $turniername $jahr";

?>
<div class="verein-page row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Torschützenliste") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                    	<?php $counter = 1;?>
                    	<?php $neueToreAnzahl = 0;?>
                        <?php foreach ($topScorers as $scorer): ?>
                        <tr>
                        	<th>
                        		<?= ($neueToreAnzahl == (int)$scorer['tor']) ? '' : $counter?>
                        	</th>
                            <td>
                                <?= Helper::getFlagInfo($scorer['nati1'], $turnierjahr, false) ?>
	                            <?php
                                    $spielername = trim(
                                        htmlspecialchars($scorer['vorname'] ?? '') . ' ' .
                                        htmlspecialchars($scorer['name'] ?? '')
                                    );
                                    ?>
								<?= Html::a($spielername, ['/spieler/view', 'id' => $scorer['id']], ['class' => 'text-decoration-none']) ?>
                            </td>
                            <td><?= SpielerHelper::getLandAtTournament($scorer['id'], $tournamentID)?></td>
                            <td>
                            	<?php $neueToreAnzahl = (int)$scorer['tor'];?>
                            	<?= (int)$scorer['tor'] . " (" . (int)$scorer['11m'] . ")" ?>
                            </td>
                        </tr>
                        <?php $counter = $counter+1;?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
            