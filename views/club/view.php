<?php
use app\components\ButtonHelper;
use app\components\ClubHelper;
use app\components\Helper;
use app\models\Club;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $club app\models\Club */
/* @var $nation app\models\Nation */
/* @var $stadium app\models\Stadion */
/* @var $recentMatches app\models\Spiel[] */
/* @var $upcomingMatches app\models\Spiel[] */
/* @var $squad app\models\Spieler[] */

$this->registerJsFile('@web/js/club.js',  ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = $isEditing
? ($club->isNewRecord ? Yii::t('app', 'Create New Club') : Yii::t('app', 'Edit Club: {name}', ['name' => $club->name]))
: $club->namevoll;
$currentYear = date('Y');
?>

<?php 
$fields = [
    ['attribute' => 'name', 'icon' => 'fas fa-shield-alt', 'options' => ['maxlength' => true]],
    ['attribute' => 'namevoll', 'icon' => 'fas fa-address-card', 'options' => ['maxlength' => true]],
    ['attribute' => 'nations', 'icon' => 'fas fa-earth-europe', 'options' => []],
    ['attribute' => 'founded', 'icon' => 'fas fa-calendar-alt', 'options' => ['type' => 'date']],
    ['attribute' => 'colors', 'icon' => 'fas fa-palette', 'options' => []],
    ['attribute' => 'stadium', 'icon' => 'fas fa-location-dot', 'options' => [], 'data' => $stadien],
    ['attribute' => 'address', 'icon' => 'fas fa-envelope', 'options' => ['maxlength' => true]],
    ['attribute' => 'telefon', 'icon' => 'fas fa-phone', 'options' => ['maxlength' => true]],
    ['attribute' => 'homepage', 'icon' => 'fas fa-laptop-code', 'options' => ['maxlength' => true]],
];
?>

<div class="verein-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-3">
        <!-- Widget 1: Vereinsdaten -->
         <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3><?= Yii::t('app', 'Club data') ?></h3></div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                            <table class="table">
                                <?php foreach ($fields as $field): ?>
                                    <?= ClubHelper::renderEditableRow($form, $club, $field['attribute'], $field['icon'], $field['options'], $field['data'] ?? null) ?>
                                <?php endforeach; ?>
                            </table>
                            <div class="form-group">
                                <?= ButtonHelper::saveButton() ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                        <?php $i = 0;?>
                            <?php foreach ($fields as $field):?>
        						<?php 
        						    // Bestimme den zu übergebenen Wert: $club oder $stadion
                                    $value = ($field['attribute'] === 'stadium') ? $stadium : $club; 
                                ?>
                                <?= ClubHelper::renderViewRow($field['attribute'], $value, $field['icon']) ?>
                                <?php $i++;?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

		<div class="col-md-2">&nbsp;</div>

        <!-- Widget 2: Zusammenfassung -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3><?= Html::encode($club->name) ?></h3></div>
                <div class="card-body d-flex align-items-center">
                    <!-- Bild als separater Div-Container -->
                    <div class="text-center" style="flex: 0 0 auto; margin-right: 20px;">
                        <img src="<?= Html::encode(Helper::getClubLogoUrl($club->id)) ?>" 
                             class="img-fluid" 
                             alt="<?= Html::encode($club->name) ?>" 
                             style="width: 100px; height: 100px;">
                    </div>
                    
                    <!-- Informationen im Row-Container -->
                    <div class="flex-grow-1">
                        <p class="text-center"><?= Html::encode($club->namevoll) ?></p>
                        <hr>
                        <div class="row">
                            <?= ClubHelper::renderCountryDivRow($club->land) ?>
                            <?= ClubHelper::renderFoundationDivRow($club->founded) ?>
                            <?= ClubHelper::renderStadiumDivRow($stadium) ?>
                            <?= ClubHelper::renderHomepageDivRow($club->homepage) ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
	<?php if ($recentMatches || $upcomingMatches): ?>
        <!-- Zweite Widgetreihe -->
        <div class="row mb-3">
            <!-- Widget 3: Letzte 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', 'Last 5 Games') ?></div>
                    <div class="card-body">
                    <?php if ($recentMatches): ?>
                        <table class="table">
                            <tbody>
                                <?php foreach ($recentMatches as $index => $match): ?>
                                    <?php 
                                        $isHome = $match->club1ID == $club->id; // Home/Away Check
                                        $opponent = $isHome ? $match->club2->name : $match->club1->name; // Gegnername
                                        $resultColor = Helper::getResultColor($isHome, $match);
                                    ?>
                                    <tr>
                                        <td style="background-color: <?= $index % 2 === 0 ? COLOR_ROW_EVEN : COLOR_ROW_ODD ?> !important;"><?= Html::encode(Yii::$app->formatter->asDate($match->turnier->datum, 'php:d.m.Y')) ?></td>
                                        <td style="background-color: <?= $index % 2 === 0 ? COLOR_ROW_EVEN : COLOR_ROW_ODD ?> !important;"><?= Html::encode(Yii::$app->formatter->asTime($match->turnier->zeit, 'php:H:i')) ?></td>
                                        <td style="background-color: <?= $index % 2 === 0 ? COLOR_ROW_EVEN : COLOR_ROW_ODD ?> !important;"><?= Html::encode($isHome ? 'H' : 'A') ?></td>
                                        <td style="background-color: <?= $index % 2 === 0 ? COLOR_ROW_EVEN : COLOR_ROW_ODD ?> !important;"><?= Html::encode($opponent) ?></td>
                                        <td style="background-color: <?= $index % 2 === 0 ? COLOR_ROW_EVEN : COLOR_ROW_ODD ?> !important;" class="<?= $resultColor ?>">
											<strong><?= $isHome ? Html::encode($match->tore1) . ':' . Html::encode($match->tore2) : Html::encode($match->tore2) . ':' . Html::encode($match->tore1) ?></strong>
                                            <?php if ($match->extratime): ?> <?= Yii::t('app', 'a.e.t.') ?><?php endif; ?>
                                            <?php if ($match->penalty): ?> <?= Yii::t('app', 'p.s.o.') ?><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p><?= Yii::t('app', 'No Games found') ?></p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
    
            <!-- Widget 4: Kommende 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', 'Next 5 Games') ?></div>
                    <div class="card-body">
                        <?php if ($upcomingMatches): ?>
                            <ul class="list-group">
                                <?php foreach ($upcomingMatches as $match): ?>
                                    <li class="list-group-item">
                                        <?= Html::encode($match->turnier->datum) ?> - 
                                        <?= Html::encode($match->club1ID == $club->id ? $match->club2->name : $match->club1->name) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><?= Yii::t('app', 'No Games planned') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>

    <!-- Widget 5: Squad -->
    <?php if ($squad || $nationalSquad): ?>
       <?php 
       // Mapping-Array für Positionen
        $positionMapping = [
            1 => Yii::t('app', 'Goal'),
            2 => Yii::t('app', 'Defender'),
            3 => Yii::t('app', 'Midfielder'),
            4 => Yii::t('app', 'Foward'),
            5 => Yii::t('app', 'Coach'),
            ];
        ?>
        <div class="card"> <!-- Gesamtrahmen für den Kader -->
            <div class="card-header">
                <h3><?= Yii::t('app', 'Squad') ?></h3> <!-- Überschrift für den gesamten Abschnitt -->
            </div>
            <div class="card-body">
                <!-- Vereins-Kader anzeigen, falls vorhanden -->
                <?php if ($squad): ?>
                    <h4><?= Yii::t('app', 'Season') ?> <?= $currentYear . '/' . ($currentYear+1); ?></h4><br>
                    <div class="row five-column-layout">
                        <?php foreach ([Yii::t('app', 'Goal'), Yii::t('app', 'Defender'), Yii::t('app', 'Midfielder'), Yii::t('app', 'Foward'), Yii::t('app', 'Coach')] as $position): ?>
                            <?php 
                            // Spieler filtern und sortieren
                            $filteredPlayers = array_filter($squad, function ($player) use ($position, $positionMapping) {
                                $positionID = $player->vereinSaison[0]->positionID ?? null;
                                $playerPositionName = $positionMapping[$positionID] ?? null;
                                return $playerPositionName === $position;
                            });
                            if (empty($filteredPlayers)) continue;
                            usort($filteredPlayers, function ($a, $b) {
                                return strcmp($a->name, $b->name);
                            });
                            ?>
                            <div class="col-5">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="title"><?= Html::encode($position) ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <?php 
                                            $counter = 0;
                                            foreach ($filteredPlayers as $player): 
                                                $backgroundStyle = $counter % 2 === 0 ? '#f0f8ff' : '#ffffff';
                                                $counter++;
                                            ?>
                                                <li class="d-flex align-items-center p-2" style="background-color: <?= $backgroundStyle ?> !important; border-width: 1px 0 0 0; border-style: solid; border-color: #e7e7e7;">
                                                    <?php if (!empty($player->nati1)): ?>
                                                        <?= Helper::renderFlag($player->nati1) ?>
                                                    <?php endif; ?>
                                                    <?= Html::a(Html::encode($player->name . ($player->vorname ? ', ' . mb_substr($player->vorname, 0, 1, 'UTF-8') . '.' : '')), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: right;">
							<?= Html::a(Yii::t('app', 'Complete Squad'), ['/kader/' . $club->id . '/' . $currentYear], ['class' => 'text-decoration-none']) ?>
						</div>
                    </div>
                <?php endif; ?>
                
                <!-- National-Kader anzeigen, falls vorhanden -->
                <?php if ($nationalSquad): ?>
                <?php 
                    $lastMatch = Club::getLastMatch($club->id);
                    
                    if (!$lastMatch) {
                        // Keine Spiele gefunden, leere Sammlung zurückgeben
                        return [];
                    }
                    
                    $wettbewerbID = $lastMatch['wettbewerbID'];
                    $jahr = $lastMatch['jahr'];
                ?>
                    <h4><?= Helper::getTurniername($wettbewerbID) . ' ' . $jahr; ?></h4><br>
                    <div class="row five-column-layout">
                         <?php foreach ([Yii::t('app', 'Goal'), Yii::t('app', 'Defender'), Yii::t('app', 'Midfielder'), Yii::t('app', 'Foward'), Yii::t('app', 'Coach')] as $position): ?>
                         	<?php 
                            // Spieler filtern und sortieren
                            $filteredPlayers = array_filter($nationalSquad, function ($player) use ($position, $positionMapping) {
                                $positionID = $player->landWettbewerb[0]->positionID ?? null;
                                $playerPositionName = $positionMapping[$positionID] ?? null;
                                return $playerPositionName === $position;
                            });
                                if (empty($filteredPlayers)) continue;
                            usort($filteredPlayers, function ($a, $b) {
                                return strcmp($a->name, $b->name);
                            });
                            ?>
                            <div class="col-5">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="title"><?= Html::encode($position) ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <?php 
                                            $counter = 0;
                                            foreach ($filteredPlayers as $player): 
                                                $backgroundStyle = $counter % 2 === 0 ? '#f0f8ff' : '#ffffff';
                                                $counter++;
                                            ?>
                                                <li class="d-flex align-items-center p-2" style="background-color: <?= $backgroundStyle ?> !important; border-width: 1px 0 0 0; border-style: solid; border-color: #e7e7e7;">
                                                    <?php if (!empty($player->nati1)): ?>
                                                        <?= Helper::renderFlag($player->nati1) ?> 
                                                    <?php endif; ?>
                                                    <?= Html::a(Html::encode($player->name . ($player->vorname ? ', ' . mb_substr($player->vorname, 0, 1, 'UTF-8') . '.' : '')), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: right;">
							<?= Html::a(Yii::t('app', 'Complete Squad'), ['/kader/' . $club->id . '/' . $jahr . '/' . $wettbewerbID], ['class' => 'text-decoration-none']) ?>
						</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php
$this->registerJs("$('.selectpicker').selectpicker();", \yii\web\View::POS_READY);
?>