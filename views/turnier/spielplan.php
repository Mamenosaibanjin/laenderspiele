<?php
use app\components\Helper;
use app\models\Runde;
use app\models\Spiel;
use app\models\Tournament;
use app\models\Turnier;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TabellenHelper;

/** @var $turnier app\models\Turnier */
/** @var $runden app\models\Runde[] */
/** @var $runde app\models\Runde */
/** @var $spiele app\models\Spiel[] */

$this->title = "Spielplan – {$turnier->jahr} ({$turnier->wettbewerb->name})";
?>


<!-- Runden-Dropdown -->
<div class="d-flex">
    <?php 
    $aktuellesTurnier = Tournament::findOne($turnier->id);
    $wettbewerbID = $aktuellesTurnier->wettbewerbID;

    $verwandteTurniere = Tournament::find()
        ->where(['wettbewerbID' => $wettbewerbID])
        ->orderBy(['jahr' => SORT_ASC])
        ->all();

    $seite = Yii::$app->request->get('seite') ?? 'spielplan';
    
    // Nav mit beiden Dropdowns
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav flex-row'],
        'items' => [

            // Turnier-Dropdown
            [
                'label' => Helper::getTurniernameFullnameForDropdown($turnier->id),
                'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                'items' => array_map(function ($t) use ($turnier, $seite) {
                    return [
                        'label' => Helper::getTurniernameFullnameForDropdown($t->id),
                        'url' => ['/turnier/' . $t->id . '/' . $seite],
                        'active' => $t->id == $turnier->id,
                    ];
                }, $verwandteTurniere),
                'dropdownOptions' => ['class' => 'scrollable-dropdown'],
            ],

        ],
    ]);
    ?>
</div>

<style>
/* Custom Scrollable Dropdown Style */
.scrollable-dropdown {
    max-height: 200px; /* ca. 5 Einträge */
    overflow-y: auto;
}
</style>

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
                <tbody>
                	<?php $aktuelleRunde = '';?>
                	<?php $aktuellesDatum = '';?>
                    <?php foreach ($spiele as $spiel): ?>
                    	<?php if ($aktuelleRunde != $spiel->runde->name) :?>
                    		<tr>
                    			<td colspan="6" class="header_green">
                    				<?= Html::a($spiel->runde->name, ['turnier/ergebnisse', 'tournamentID' => $turnier->id, 'rundeID' => $spiel->runde->id], ['class' => 'text-decoration-none']); ?>
                    			</td>
                    		</tr>
                  		<?php endif;?>
                        <tr>
                            <td>
                            	<?php if ($aktuellesDatum != $spiel->datum) : ?>
                            		<?= Yii::$app->formatter->asDate($spiel->datum) ?>
                            	<?php endif;?>
                            </td>
                            <td><?= Yii::$app->formatter->asTime($spiel->zeit, 'short') ?></td>
                            <td align="right">
                                <?php
                                    $club1Name = Html::a($spiel->spiel->club1->name, ['club/view', 'id' => $spiel->spiel->club1ID], ['class' => 'text-decoration-none']);
                                    $club2Name = Html::a($spiel->spiel->club2->name, ['club/view', 'id' => $spiel->spiel->club2ID], ['class' => 'text-decoration-none']);
                                ?>
                                <?= $club1Name  . " " . Helper::getFlagInfo(Helper::getClubNation($spiel->club1->id), $turnier->startdatum, false) ?>
                            </td>
                            <td align="center">–</td>
                            <td><?= Helper::getFlagInfo(Helper::getClubNation($spiel->club2->id), $turnier->startdatum, false) . " " .$club2Name ?></td>

                            <td align="center"><?= Html::a($spiel->getErgebnisHtml(), ['/spielbericht/view', 'id' => $spiel->spiel->id], ['class' => 'text-decoration-none']) ?>
                        </tr>
                        <?php $aktuelleRunde = $spiel->runde->name; ?>
                        <?php $aktuellesDatum = $spiel->datum; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        </div>
    </div>

</div>
