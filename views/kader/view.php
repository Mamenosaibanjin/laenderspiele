<?php
use yii\helpers\Html;
use app\components\Helper;

// Positionen-Mapping
$positionMapping = [
    1 => 'Tor',
    2 => 'Abwehr',
    3 => 'Mittelfeld',
    4 => 'Sturm',
    5 => 'Trainer',
    6 => 'Co-Trainer',
    7 => 'Torwart-Trainer',
];

$currentPositionID = null;
?>

<div class="card">
    <div class="card-header">
        <h3>
            <?= Html::img(Helper::getClubLogoUrl($club->id), ['alt' => Helper::getClubName($club->id), 'style' => 'height: 30px; padding-right: 10px;']) ?>
            <?= Html::encode(Helper::getClubName($club->id)) ?> - Kader 
            <?php if ($turnier == ''): ?>
            	<?= Html::encode($jahr . '/' . ($jahr + 1)); ?>
			<?php else: ?>
            	<?= Html::encode(Helper::getTurniername($turnier) . ' ' . $jahr); ?>
			<?php endif; ?>            	
        </h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Spieler</th>
                    <th>Geburtstag</th>
                    <th>
                        <?= $turnier == '' ? 'Im Verein seit' : 'Verein(e)' ?>
                    </th>
                    <?php if ($turnier == '') :?>
                    	<th>Vorheriger Club</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php $counter = 0; ?> 
                <?php foreach ($squad as $player): ?>
			        <?php 
			             $backgroundStyle = $counter % 2 === 0 ? '#f0f8ff' : '#ffffff';
			             $counter++;?>
                    <?php
                    // Bestimme die aktuelle PositionID
                    $relation = $turnier == '' ? $player->vereinSaison : $player->landWettbewerb;
                    $positionID = $relation[0]->positionID ?? 0;
                    
                    // Neue Position? Überschrift setzen
                    if ($currentPositionID !== $positionID) {
                        $currentPositionID = $positionID;
                        $positionName = $positionMapping[$positionID] ?? 'Unbekannt';
                        echo "<tr><td colspan='4'><h4>" . Html::encode($positionName) . "</h4></td></tr>";
                    }
                    ?>
                    <tr>
                        <td style="background-color: <?= $backgroundStyle; ?> !important; width: <?= $turnier == '' ? '30%' : '40%'?>;">
                            <?php if (($turnier == '') OR ($positionID > 4)) :?>
                            	<?php if (!empty($player->nati1)): ?>
                                	<img src="<?= Html::encode(Helper::getFlagUrl($player->nati1)) ?>" alt="<?= Html::encode($player->nati1) ?>" style="width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey;">
	                            <?php endif; ?>
                            <?php endif; ?>
                            <?= Html::a(Html::encode(($player->vorname . ($player->vorname ? ' ' : '')) . $player->name), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']) ?>
                        </td>
                        <td style="background-color: <?= $backgroundStyle; ?> !important; width: 20%;"><?= Yii::$app->formatter->asDate($player->geburtstag, 'php:d.m.Y') ?></td>
                        <?php if ($turnier == ''): ?>
                        	<td style="background-color: <?= $backgroundStyle; ?> !important; width: 20%;"><?= Html::encode(Helper::getImVereinSeit($player, $club->id, $jahr)) ?></td>
                        <?php else: ?>
                        	<td style="background-color: <?= $backgroundStyle; ?> !important; width: 40%;">
                        		<?php 
                        		if ($positionID <= 4) {
                            		$vereine = Helper::getClubsAtTurnier($player->id, $turnier, $jahr);
    
                            		// Nur wenn $vereine ein Array ist, iterieren
                            		if (!empty($vereine)) {
                            		    foreach ($vereine as $clubID) {
                            		        $clubName = Helper::getClubName($clubID);
                            		        
                            		        echo "<div style='padding: 5px 0;'>";
                            		        echo Html::img(Helper::getFlagUrl(Helper::getClubNation($clubID)), ['alt' => Html::encode(Helper::getClubName($clubID)), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']);
                                	        echo Html::img(Helper::getClubLogoUrl($clubID), ['alt' => Html::encode($clubName), 'style' => 'height: 30px; padding-right: 10px;']);
                            		        echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                            		        echo "</div>";
                            		    }
                            		} else {
                            		    echo "unbekannt/vereinslos";
                            		}
                        		}
                        		?>
                        	</td>
                        <?php endif; ?>
						<?php if ($turnier == '') :?>
                            <td style="background-color: <?= $backgroundStyle; ?> !important; width: 30%;">
                                <?php if ($positionID <= 4): ?>
                                    <?php
                                    // Ermitteln des "Im Verein seit"-Datums für den Spieler
                                    $imVereinSeit = Helper::getImVereinSeit($player, $club->id, $jahr);
                                    
                                    // Wenn ein Datum vorhanden ist, verwandle es in das gewünschte Format (YYYYMM07)
                                    if (!empty($imVereinSeit)) {
                                        $month = $imVereinSeit . '07'; // YYYY07
                                        
                                        // Verein vor dem aktuellen Saisonstart ermitteln
                                        $vereinVorher = $player->getVereinVorSaison($month);
    
                                        // VereinID und Jugend-Status aus dem Ergebnis extrahieren
                                        $clubID = $vereinVorher['vereinID'] ?? null;
                                        $isJugend = $vereinVorher['jugend'] ?? null;
                                        
                                        if ($clubID) {
                                            $clubName = Helper::getClubName($clubID);
                                            
                                            if ($isJugend) {
                                                // Jugendverein formatieren
                                                echo Html::img(Helper::getClubLogoUrl($clubID), ['alt' => Html::encode($clubName), 'style' => 'height: 30px; padding-right: 10px;']);
                                                echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']) . ' Jugend';
                                            } else {
                                                // Normaler Verein
                                                echo Html::img(Helper::getClubLogoUrl($clubID), ['alt' => Html::encode($clubName), 'style' => 'height: 30px; padding-right: 10px;']);
                                                echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                                            }
                                        } else {
                                            echo 'Kein vorheriger Verein gefunden';
                                        }
                                    }
                                    
                                    $clubName = Helper::getVorherigerClub($player, $club->id, $jahr);
                                    $clubID = Helper::getVorherigerClubID($player, $club->id, $jahr);
    
                                    if (strpos($clubName, 'eigene') !== false) {
                                        echo Html::encode($clubName);
                                    } else {
                                        if (strpos($clubName, 'Jugend') !== false) {
                                            $clubNameParts = explode('Jugend', $clubName, 2);
                                            echo Html::a(Html::encode(trim($clubNameParts[0])), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                                            echo ' Jugend' . Html::encode($clubNameParts[1] ?? '');
                                        } else {
                                            echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                                        }
                                    }
                                    ?>
	                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
