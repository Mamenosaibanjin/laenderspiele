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

$this->title = "Alle Torjäger - $turniername";

?>
<div class="verein-page row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Alle Torjäger") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                	<thead>
                		<th>Saison</th>
                		<th>Torschützenkönig</th>
                		<th>Mannschaft</th>
                		<th>Tore</th>
                	</thead>
                	<tbody>
                        <?php foreach ($turniere as $turnier): ?>
                            <?php 
                                $torschuetzenkoenige = TurnierHelper::getTorschuetzenkoenig($turnier['id']);
                                if (empty($torschuetzenkoenige)) continue;
                            ?>
                        
                            <!-- Abschnittsüberschrift -->
                            <tr class="table-secondary fw-bold">
                                <td colspan="4">
                                    <?= Html::a(Helper::getTurnierJahr($turnier['id']), ['/turnier/' . $turnier['id'] . '/spielplan'], ['class' => 'text-decoration-none']) ?>
                                    <span class="float-end"><?= $torschuetzenkoenige[0]['tore'] ?> Tore</span>
                                </td>
                            </tr>
                        
                            <?php foreach ($torschuetzenkoenige as $koenig): ?>
                                <tr>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 40%;"><?= Html::a(Helper::getSpielerName($koenig['spielerID']), ['/spieler/' . $koenig['spielerID']], ['class' => 'text-decoration-none']) ?></td>
                                    <td style="width: 40%;"><?= SpielerHelper::getLandAtTournament($koenig['spielerID'], $turnier['id']) ?></td>
                                    <td style="width: 10%; text-align: right;"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
            