<?php
use app\components\Helper;
use app\models\Spiel;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TabellenHelper;

/** @var $turnier app\models\Turnier */
/** @var $runden app\models\Runde[] */
/** @var $runde app\models\Runde */
/** @var $spiele app\models\Spiel[] */

$this->title = "Ergebnisse – {$turnier->jahr} ({$turnier->wettbewerb->name})";
?>


<!-- Runden-Dropdown -->
            <div class="d-flex">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav flex-row'],
                'items' => [
                    [
                        'label' => 'Wettbewerbe Männer',
                        'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                        'items' => array_map(function ($turnier) {
                            return [
                                'label' => $turnier['name'] . ' ' . $turnier['jahr'],
                                'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                            ];
                        }, Helper::getTurniere('M')),
                    ],
                    [
                        'label' => 'Wettbewerbe Frauen',
                        'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                        'items' => array_map(function ($turnier) {
                            return [
                                'label' => $turnier['name'] . ' ' . $turnier['jahr'],
                                'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                            ];
                        }, Helper::getTurniere('W')),
                    ],
                ],
            ]) ?>
        </div>
<div class="container mt-3">

<?php 
// Flaggenanzeige vorbereiten
$flaggen = '';
$laenderKeys = !empty($turnier->land) ? explode('/', $turnier->land) : [];
foreach ($laenderKeys as $key) {
    $startdatum = $turnier->startdatum ? substr($turnier->startdatum, 0, 4) . '-' . substr($turnier->startdatum, 4, 2) . '-01' : null;
    $flaggen .= Helper::getFlagInfo($key, $startdatum, false);
}
?>

    <!-- Widget 1: Ergebnisse -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0"><?= $flaggen ?> <?= Html::encode($turnier->wettbewerb->name) ?> <?= Html::encode($turnier->jahr) ?></h3>
        </div>
        <div class="card-body">

            <!-- Normale Tabelle ohne Eingabe -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Zeit</th>
                        <th>Heim</th>
                        <th></th>
                        <th>Auswärts</th>
                        <th>Ergebnis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($spiele as $spiel): ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($spiel->datum) ?></td>
                            <td><?= Yii::$app->formatter->asTime($spiel->zeit, 'short') ?></td>
                            <td align="right">
                                <?php
                                    $club1Name = Html::a($spiel->spiel->club1->name, ['club/view', 'id' => $spiel->spiel->club1ID], ['class' => 'text-decoration-none']);
                                    $club2Name = Html::a($spiel->spiel->club2->name, ['club/view', 'id' => $spiel->spiel->club2ID], ['class' => 'text-decoration-none']);
                        
                                    // Gewinnerlogik nur bei KO-Runden
                                    if ($spiel->runde?->typ === 'ko' && is_numeric($spiel->spiel->tore1) && is_numeric($spiel->spiel->tore2)) {
                                        if ($spiel->spiel->tore1 > $spiel->spiel->tore2) {
                                            $club1Name = '<strong>' . $club1Name . '</strong>';
                                        } elseif ($spiel->spiel->tore2 > $spiel->spiel->tore1) {
                                            $club2Name = '<strong>' . $club2Name . '</strong>';
                                        }
                                    }
                                ?>
                                <?= $club1Name ?>
                            </td>
                            <td align="center">–</td>
                            <td><?= $club2Name ?></td>
                            <td><?= Html::a($spiel->spiel->tore1 . ':' .  $spiel->spiel->tore2, ['spielbericht/view', 'id' => $spiel->spiel->id], ['class' => 'text-decoration-none']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        </div>
    </div>

	<?php if ($spiel->runde?->typ === 'gruppe') : ?>
        <!-- Widget 2: Optional Tabelle oder andere Daten -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Tabelle</h3>
            </div>
            <div class="card-body">
                <?php
                
                $rundeID = Yii::$app->request->get('rundeID'); // später dynamisch
                $spieltagMax = 3;
                $daten = TabellenHelper::berechneTabelle($turnier->id, $rundeID, $spieltagMax);
                $farben = TabellenHelper::getPlatzfarben($turnier->id, $rundeID);
                ?>
                
                <table class="table table-bordered table-sm text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th style="text-align:left;">Mannschaft</th>
                            <th>Sp.</th>
                            <th>S</th>
                            <th>U</th>
                            <th>N</th>
                            <th>Tore</th>
                            <th>Dif.</th>
                            <th>Pkt.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $platz = 1; foreach ($daten as $club): ?>
                            <?php
                                $diff = $club['tore'] - $club['gegentore'];
                                $farbe = $farben[$platz] ?? null;
                            ?>
                            <tr style="<?= $farbe ? "background-color: $farbe;" : '' ?>">
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $platz ?></td>
                                <td style="text-align:left; <?= $farbe ? "background-color: $farbe;" : '' ?>"><?= Html::a($club['club']->name, ['club/view', 'id' => $club['club']->id], ['class' => 'text-decoration-none']) ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['spiele'] ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['siege'] ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['remis'] ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['niederlagen'] ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['tore'] ?>:<?= $club['gegentore'] ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $diff ?></td>
                                <td style="<?= $farbe ? "background-color: $farbe;" : '' ?>"><?= $club['punkte'] ?></td>
                            </tr>
                        <?php $platz++; endforeach; ?>
                    </tbody>
                </table>
    
            </div>
   		 </div>
	<?php endif; ?>
</div>
