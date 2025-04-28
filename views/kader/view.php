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

// Entscheidung: Nationalmannschaft oder Verein
$isNationalTeam = in_array($club->typID, [1, 2]);

$currentPositionID = null;
?>

<div class="card">
    <div class="card-header">
        <h3>
            <?= Html::img(Helper::getClubLogoUrl($club->id), ['alt' => Helper::getClubName($club->id), 'style' => 'height: 30px; padding-right: 10px;']) ?>
            <?= Html::encode(Helper::getClubName($club->id)) ?> - Kader <?= Html::encode(Helper::getKaderJahr($tournament, $tournamentID)) ?>

        </h3>
    </div>

    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Spieler</th>
                    <th>Geburtstag</th>
                    <th><?= $isNationalTeam ? 'Verein(e)' : 'Im Verein seit' ?></th>
                    <?php if (!$isNationalTeam): ?>
                        <th>Vorheriger Club</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php $counter = 0; ?>
                <?php foreach ($squad as $player): ?>
                    <?php
                    $backgroundStyle = $counter++ % 2 === 0 ? '#f0f8ff' : '#ffffff';
                    $relation = $isNationalTeam ? $player->landWettbewerb : $player->vereinSaison;
                    $positionID = $relation[0]->positionID ?? 0;

                    if ($currentPositionID !== $positionID) {
                        $currentPositionID = $positionID;
                        $positionName = $positionMapping[$positionID] ?? 'Unbekannt';
                        echo "<tr><td colspan='4'><h4>" . Html::encode($positionName) . "</h4></td></tr>";
                    }
                    ?>
                    <tr>
                        <!-- Spielername -->
                        <td style="background-color: <?= $backgroundStyle ?>; width: <?= $isNationalTeam ? '40%' : '30%' ?>;">
                            <?php if (!$isNationalTeam || $positionID > 4): ?>
                                <?= !empty($player->nati1) ? Helper::getFlagInfo($player->nati1, null, false) : '' ?>
                            <?php endif; ?>
                            <?= Html::a(Html::encode(trim($player->vorname . ' ' . $player->name)), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']) ?>
                        </td>

                        <!-- Geburtstag -->
                        <td style="background-color: <?= $backgroundStyle ?>; width: 20%;">
                            <?= Yii::$app->formatter->asDate($player->geburtstag, 'php:d.m.Y') ?>
                        </td>

                        <!-- Verein(e) oder "Im Verein seit" -->
                        <td style="background-color: <?= $backgroundStyle ?>;">
                            <?php if ($isNationalTeam): ?>
                                <?php
                                $tournamentID = $relation[0]->tournamentID ?? null;
                                $vereine = Helper::getClubsAtTurnier($player->id, $tournamentID, $tournament->jahr);
                                if (!empty($vereine)) {
                                    foreach ($vereine as $clubID) {
                                        $clubName = Helper::getClubName($clubID);
                                        echo "<div style='padding: 5px 0;'>";
                                        echo Helper::getFlagInfo(Helper::getClubNation($clubID), $tournament->jahr . '-07-01', false);
                                        echo Html::img(Helper::getClubLogoUrl($clubID), ['alt' => Html::encode($clubName), 'style' => 'height: 30px; padding-right: 10px;']);
                                        echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                                        echo "</div>";
                                    }
                                } else {
                                    echo "unbekannt/vereinslos";
                                }
                                ?>
                            <?php else: ?>
                                <?= Html::encode(Helper::getImVereinSeit($player, $club->id, $tournament->jahr)) ?>
                            <?php endif; ?>
                        </td>

                        <!-- Vorheriger Club (nur Verein) -->
                        <?php if (!$isNationalTeam): ?>
                            <td style="background-color: <?= $backgroundStyle ?>;">
                                <?php
                                $imVereinSeit = Helper::getImVereinSeit($player, $club->id, $tournament->jahr);
                                if (!empty($imVereinSeit)) {
                                    $month = $imVereinSeit . '07';
                                    $vereinVorher = $player->getVereinVorSaison($month);

                                    $clubID = $vereinVorher['vereinID'] ?? null;
                                    $isJugend = $vereinVorher['jugend'] ?? null;

                                    if ($clubID) {
                                        $clubName = Helper::getClubName($clubID);
                                        echo Html::img(Helper::getClubLogoUrl($clubID), ['alt' => Html::encode($clubName), 'style' => 'height: 30px; padding-right: 10px;']);
                                        echo Html::a(Html::encode($clubName), ['/club/view', 'id' => $clubID], ['class' => 'text-decoration-none']);
                                        echo $isJugend ? ' Jugend' : '';
                                    } else {
                                        echo 'Kein vorheriger Verein gefunden';
                                    }
                                }
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        
<div id="spieler-zuordnung-container">
    <div class="mb-3">
        <input type="text" class="form-control" id="spielerKaderSearch" placeholder="Spieler suchen..."><br>
        <button type="button" class="btn btn-primary mt-2" id="btn-spieler-bearbeiten" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/new', '_blank')">
            Neuer Spieler
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('spielerKaderSearch');
    const bearbeitenButton = document.getElementById('btn-spieler-bearbeiten');
    let selectedSpielerID = null;

    if (searchInput) {
        if (!searchInput.awesomplete) {
            const awesomplete = new Awesomplete(searchInput, {
                minChars: 2,
                autoFirst: true,
                replace: function (suggestion) {
                    this.input.value = suggestion.label;
                },
            });
            searchInput.awesomplete = awesomplete;
        }

        searchInput.addEventListener('input', function () {
            const term = searchInput.value.trim();
            if (term.length < 2) {
                bearbeitenButton.textContent = "Neuer Spieler";
                bearbeitenButton.setAttribute('onclick', "window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/new', '_blank')");
                bearbeitenButton.disabled = false;
                selectedSpielerID = null;
                return;
            }

            fetch(`<?= \yii\helpers\Url::to(['spieler/search']) ?>?term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        searchInput.awesomplete.list = data.map(item => ({
                            label: item.value,
                            value: item.id,
                        }));
                    } else {
                        searchInput.awesomplete.list = [];
                        bearbeitenButton.textContent = "Neuer Spieler";
                        bearbeitenButton.setAttribute('onclick', "window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/new', '_blank')");
                        bearbeitenButton.disabled = false;
                        selectedSpielerID = null;
                    }
                })
                .catch(error => {
                    console.error('Fehler bei der Suche:', error);
                    searchInput.awesomplete.list = [];
                    bearbeitenButton.textContent = "Neuer Spieler";
                    bearbeitenButton.setAttribute('onclick', "window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/new', '_blank')");
                    bearbeitenButton.disabled = false;
                    selectedSpielerID = null;
                });
        });

        searchInput.addEventListener('awesomplete-selectcomplete', function (event) {
            selectedSpielerID = event.text.value;
            searchInput.value = event.text.label;

            bearbeitenButton.textContent = "Spieler bearbeiten";
            bearbeitenButton.setAttribute('onclick', `window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/spieler/${selectedSpielerID}', '_blank')`);
            bearbeitenButton.disabled = false;
        });
    }
});
</script>

    </div>
</div>