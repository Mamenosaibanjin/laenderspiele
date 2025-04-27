<?php
use app\components\Helper;
use app\models\Spiel;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

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
            

        <?php if (!Yii::$app->user->isGuest): ?>

        <?php else: ?>
        
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
                	<?php 
                	$spiele = Spiel::find()
                	->where(['tournamentID' => $turnier->id])
                	->orderBy(['datum' => SORT_ASC, 'zeit' => SORT_ASC])
                	->all();?>
                    <?php foreach ($spiele as $spiel): ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($spiel->datum) ?></td>
                            <td><?= Yii::$app->formatter->asTime($spiel->zeit, 'short') ?></td>
                            <td><?= Html::a($spiel->club1->name, ['club/view', 'id' => $spiel->club1ID]) ?></td>
                            <td>–</td>
                            <td><?= Html::a($spiel->club2->name, ['club/view', 'id' => $spiel->club2ID]) ?></td>
                            <td><?= Html::a($spiel->ergebnis ?? '– : –', ['spiel/view', 'id' => $spiel->id]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        
        <?php endif; ?>

        </div>
    </div>

    <!-- Widget 2: Optional Tabelle oder andere Daten -->
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Tabelle</h3>
        </div>
        <div class="card-body">
            <!-- Optionaler Platzhalter: Du kannst hier z. B. die Gruppentabelle einfügen -->
            <p class="text-muted">Hier könnten Gruppentabellen, Statistiken o. Ä. folgen …</p>
        </div>
    </div>
</div>
