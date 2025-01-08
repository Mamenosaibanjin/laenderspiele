<?php
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
? ($club->isNewRecord ? 'Neuen Club erstellen' : 'Club bearbeiten: ' . $club->name)
: $club->namevoll;
$currentYear = date('Y');
?>

<div class="verein-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-3">
        <!-- Widget 1: Vereinsdaten -->
         <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3>Vereinsdaten</h3></div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                        <table class="table">
                            <tr>
                                <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                                <td><?= $form->field($club, 'name')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-address-card"></i></th>
                                <td><?= $form->field($club, 'namevoll')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-earth-europe"></i></th>
                                <td>
                                    <?= $form->field($club, 'land')->dropDownList(
                                        \yii\helpers\ArrayHelper::map($nationen, 'kuerzel', 'land_de'),
                                        ['prompt' => 'Wähle ein Land']
                                    )->label(false) ?>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-alt"></i></th>
                                <td><?= $form->field($club, 'founded')->input('date')->label(false) ?></td>
                            </tr>

                            <tr>
                                <th><i class="fas fa-palette"></i></th>
                                <td>
                                        <?= $form->field($club, 'farben')->hiddenInput(['id' => 'farben-input', 'class' => 'form-control', 'value' => Html::encode($club->farben)])->label(false) ?>
                                    	<div id="color-picker-container"></div>
                                        <button type="button" id="add-color" class="btn btn-secondary btn-sm mt-2">Farbe hinzufügen</button>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-location-dot"></i></th>
                            <td>
                                <?= $form->field($club, 'stadionID')->hiddenInput([
                                    'id' => 'hidden-stadion-id', 
                                    'value' => $club->stadionID,
                                ])->label(false); ?>
                            
                                <?php
                                // Stadionname anhand der ID vorfüllen
                                $stadionName = '';
                                if (!empty($club->stadionID)) {
                                    foreach ($stadien as $stadion) {
                                        if ($stadion['id'] == $club->stadionID) {
                                            $stadionName = $stadion['name'];
                                            break;
                                        }
                                    }
                                }
                                ?>
                            
                                <?php
                                $stadienData = array_map(function ($stadion) {
                                    return [
                                        'label' => $stadion['name'] . ', ' . $stadion['stadt'],
                                        'value' => $stadion['id'],
                                        'klarname' => $stadion['name']
                                    ];
                                }, $stadien);
                            
                                $stadienDataJson = json_encode($stadienData);?>

                               <?= Html::textInput('stadionName', $stadionName, [
                                    'id' => 'autocomplete-stadion',
                                   'class' => 'form-control',
                                   'data-stadien' => $stadienDataJson // Daten über ein data-Attribut übergeben
                               ]); ?><br>
                            
                            </td>
                            
                            </tr>

                            <tr>
                                <th><i class="fas fa-envelope"></i></th>
                                <td>
                                    <?= $form->field($club, 'postfach')->textInput(['maxlength' => true])->label('Postfach') ?>
                                    <?= $form->field($club, 'strasse')->textInput(['maxlength' => true])->label('Straße') ?>
                                    <?= $form->field($club, 'ort')->textInput(['maxlength' => true])->label('Ort') ?>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-phone"></i></th>
                                <td><?= $form->field($club, 'telefon')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-laptop-code"></i></th>
                                <td><?= $form->field($club, 'homepage')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                        </table>
                        <div class="form-group">
                            <?= Html::submitButton('Speichern', ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                            <tr>
                                <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                                <td><?= Html::encode($club->name) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-address-card"></i></th>
                                <td><?= Html::encode($club->namevoll) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-earth-europe"></i></th>
                                <td>
                                    <?= Helper::getFlagUrl($club->land) ? Html::img(Helper::getFlagUrl($club->land), ['alt' => $nation->land_de , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) : '' ?>
                                    <?= Html::encode($nation->land_de) ?>
                                </td>
                            </tr>
                            <?php if ($club->founded): ?>
                                <tr>
                                    <th><i class="fas fa-calendar-alt"></i></th>
                                    <td><?= Html::encode(DateTime::createFromFormat('Y-m-d', $club->founded)->format('d.m.Y')) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($club->farben): ?>
                                <tr>
                                    <th><i class="fas fa-palette"></i></th>
                                    <td>
                                        <?php 
                                        $colors = explode('-', $club->farben);
                                        $lastIndex = count($colors) - 1; // Index der letzten Farbe
                                        ?>
                                        <?php foreach ($colors as $index => $color): ?>
                                            <span 
                                                style="
                                                    display:inline-block; 
                                                    width:20px; 
                                                    height:20px; 
                                                    background-color:<?= strpos($color, '#') === 0 ? $color : (Html::encode(Helper::colorToHex($color))) ?>; 
                                                    border:1px solid #000; 
                                                    <?= $index === 0 ? 'border-radius: 10px 0 0 10px;' : '' ?> 
                                                    <?= $index === $lastIndex ? 'border-radius: 0 10px 10px 0;' : '' ?> 
                                                    <?= $index !== $lastIndex ? 'margin-right: -5px;' : '' ?>
                                                ">
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!is_null($stadium)): ?>
                                <tr>
                                    <th><i class="fas fa-location-dot"></i></th>
                                    <td>
                                        <?= Html::encode($stadium->name) ?><br>
                                        <?= Html::encode($stadium->kapazitaet) ?> Plätze
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th><i class="fas fa-envelope"></i></th>
                                <td>
                                    <?= Html::encode($club->name) ?><br>
                                    <?= $club->postfach ? 'Postfach ' . Html::encode($club->postfach) . '<br>' : '' ?>
                                    <?= $club->strasse ? nl2br(Html::encode($club->strasse)) . '<br>' : '' ?>
                                    <?= $club->ort ? Html::encode($club->ort) . '<br>' : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-phone"></i></th>
                                <td><?= Html::encode($club->telefon) ?></td>
                            </tr>
                            <?php if ($club->homepage): ?>
                                <tr>
                                    <th><i class="fas fa-laptop-code"></i></th>
                                    <td><?= Html::a($club->homepage, 'http://' . $club->homepage, ['target' => '_blank']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>


		<div class="col-md-2">
		&nbsp;</div>

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
                            <div class="col-2" style="text-align: right;"><i class="fas fa-earth-europe"></i></div>
                            <div class="col-10" style="text-align: left;">
                				<?= Helper::getFlagUrl($club->land) ? Html::img(Helper::getFlagUrl($club->land), ['alt' => $nation->land_de , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) : '' ?>
                                <?= Html::encode($nation->land_de) ?></div>
	
	                        <?php if ($club->founded) :?>
                            <div class="col-2" style="text-align: right;"><i class="fas fa-calendar-alt"></i></div>
                            <div class="col-10" style="text-align: left;"><?= Html::encode(DateTime::createFromFormat('Y-m-d', $club->founded)->format('d.m.Y')) ?></div>
                            <?php endif; ?>
                            
							<?php if (!is_null($stadium)): ?>
                                <div class="col-2" style="text-align: right;"><i class="fas fa-location-dot"></i></div>
                                <div class="col-10" style="text-align: left;"><?= Html::encode($stadium->name) ?></div>
        					<?php endif; ?>
                                
                            <?php if (!empty($stadium)): ?>
                                <div class="col-2" style="text-align: right;"><i class="fas fa-laptop-code"></i></div>
                                <div class="col-10" style="text-align: left;">
                            <?php endif; ?>
                            <?= Html::a($club->homepage, $club->homepage, ['target' => '_blank']) ?>
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
                    <div class="card-header">Letzte 5 Spiele</div>
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
                                            <?php if ($match->extratime): ?> n.V.<?php endif; ?>
                                            <?php if ($match->penalty): ?> i.E.<?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Keine Spiele gefunden.</p>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
    
            <!-- Widget 4: Kommende 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Kommende 5 Spiele</div>
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
                            <p>Keine Spiele geplant.</p>
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
            1 => 'Tor',
            2 => 'Abwehr',
            3 => 'Mittelfeld',
            4 => 'Sturm',
            5 => 'Trainer',
        ];
        ?>
        <div class="card"> <!-- Gesamtrahmen für den Kader -->
            <div class="card-header">
                <h3>Kader</h3> <!-- Überschrift für den gesamten Abschnitt -->
            </div>
            <div class="card-body">
                <!-- Vereins-Kader anzeigen, falls vorhanden -->
                <?php if ($squad): ?>
                    <h4>Saison <?= $currentYear . '/' . ($currentYear+1); ?></h4><br>
                    <div class="row five-column-layout">
                        <?php foreach (['Tor', 'Abwehr', 'Mittelfeld', 'Sturm', 'Trainer'] as $position): ?>
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
                                                        <img src="<?= Html::encode(Helper::getFlagUrl($player->nati1)) ?>" 
                                                             alt="<?= Html::encode($player->nati1) ?>" 
                                                             style="width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;">
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
							<?= Html::a('Kompletter Kader', ['/kader/' . $club->id . '/' . $currentYear], ['class' => 'text-decoration-none']) ?>
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
                        <?php foreach (['Tor', 'Abwehr', 'Mittelfeld', 'Sturm', 'Trainer'] as $position): ?>
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
                                                        <img src="<?= Html::encode(Helper::getFlagUrl($player->nati1)) ?>" 
                                                             alt="<?= Html::encode($player->nati1) ?>" 
                                                             style="width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;">
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
							<?= Html::a('Kompletter Kader', ['/kader/' . $club->id . '/' . $jahr . '/' . $wettbewerbID], ['class' => 'text-decoration-none']) ?>
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