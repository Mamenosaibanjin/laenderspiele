<?php
use app\components\ButtonHelper;
use app\components\ClubHelper;
use app\components\GameHelper;
use app\components\PositionHelper;
use app\components\Helper;
use app\models\Club;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\components\SquadHelper;

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
 
            <!-- Widget: Letzte 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', Yii::t('app', 'Last {number} Games', ['number' => 5])) ?></div>
                    <div class="card-body">
                        <?= GameHelper::renderMatchWidget(Yii::t('app', Yii::t('app', 'Last {number} Games', ['number' => 5])), $recentMatches, $club, Yii::t('app', 'No Games found')) ?>
                    </div>
                </div>
            </div>
    
            <!-- Widget: Kommende 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', Yii::t('app', 'Next {number} Games', ['number' => 5])) ?></div>
                    <div class="card-body">
                        <?= GameHelper::renderMatchWidget(Yii::t('app', Yii::t('app', 'Next {number} Games', ['number' => 5])), $upcomingMatches, $club, Yii::t('app', 'No Games planned')) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Widget 5: Squad -->
    <?php if ($squad || $nationalSquad): ?>
       <?php 
       // Mapping-Array für Positionen
        $positionMapping = PositionHelper::getMapping(['TW', 'AB', 'MF', 'ST', 'TR']);
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
                		<?php foreach ($positionMapping as $positionID => $positionName): ?>
                            <?php 
                            // Spieler filtern und sortieren
                            $filteredPlayers = SquadHelper::getFilteredAndSortedPlayers($squad, $positionID);
                            
                            if (empty($filteredPlayers)) continue;

                            ?>
                            <div class="col-5">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="title"><?= Html::encode($positionName) ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <?php foreach ($filteredPlayers as $player): ?>
                                                <li>
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
                		<?php foreach ($positionMapping as $positionID => $positionName): ?>
                         	<?php 
                            // Spieler filtern und sortieren
                         	$filteredPlayers = SquadHelper::getFilteredAndSortedPlayers($nationalSquad, $positionID);
                            
                         	if (empty($filteredPlayers)) continue;
                            ?>
                            <div class="col-5">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="title"><?= Html::encode($positionName) ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <?php 
                                            $index = 0;
                                            foreach ($filteredPlayers as $player):
                                                $index++;
                                                ?>
                                                <li>
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