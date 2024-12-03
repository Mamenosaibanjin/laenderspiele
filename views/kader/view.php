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
            <?= Html::encode(Helper::getClubName($club->id)) ?> - Kader <?= Html::encode($jahr . '/' . ($jahr + 1)) ?>
        </h3>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Spieler</th>
                    <th>Geburtstag</th>
                    <th>Im Verein seit</th>
                    <th>Vorheriger Club</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($squad as $player): ?>
                    <?php
                    // Bestimme die aktuelle PositionID
                    $positionID = $player->vereinSaison[0]->positionID ?? 0;

                    // Neue Position? Überschrift setzen
                    if ($currentPositionID !== $positionID) {
                        $currentPositionID = $positionID;
                        $positionName = $positionMapping[$positionID] ?? 'Unbekannt';
                        echo "<tr><td colspan='4'><h4>" . Html::encode($positionName) . "</h4></td></tr>";
                    }
                    ?>
                    <tr>
                        <td style="width: 30%;">
                            <?php if (!empty($player->nati1)): ?>
                                <img src="<?= Html::encode(Helper::getFlagUrl($player->nati1)) ?>" alt="<?= Html::encode($player->nati1) ?>" style="width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey;">
                            <?php endif; ?>
                            <?= Html::a(Html::encode(($player->vorname . ($player->vorname ? ' ' : '')) . $player->name), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']) ?>
                        </td>
                        <td style="width: 20%;"><?= Yii::$app->formatter->asDate($player->geburtstag, 'php:d.m.Y') ?></td>
                        <td style="width: 20%;"><?= Html::encode(Helper::getImVereinSeit($player, $club->id, $jahr)) ?></td>
                        <td style="width: 30%;">
                            <?php if ($positionID <= 4): ?>
                                <?= Html::img(Helper::getClubLogoUrl(Helper::getVorherigerClubID($player, $club->id, $jahr)), ['alt' => Helper::getVorherigerClub($player, $club->id, $jahr), 'style' => 'height: 30px;']) ?>
                                <?php
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
