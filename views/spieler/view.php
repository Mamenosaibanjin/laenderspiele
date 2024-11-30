<?php
use yii\helpers\Html;
use app\components\Helper;

/** @var $spieler app\models\Spieler */
/** @var $vereinsKarriere app\models\SpielerVereinSaison[] */
/** @var $jugendvereine app\models\SpielerVereinSaison[] */
/** @var $laenderspiele app\models\SpielerLandWettbewerb */

$this->title = $spieler->fullname;
?>

<!-- Spieler-Seite: Header -->
<div class="container">
    <!-- Widget 1: Allgemeine Spielerdaten -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3><?= Html::encode(trim(($spieler->vorname ?? '') . ' ' . $spieler->name)) ?></h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <!-- Vorname -->
                        <?php if ($spieler->vorname): ?>
                            <tr>
                                <th style="width: 40px;"><i class="fas fa-signature"></i></th>
                                <td><?= Html::encode($spieler->vorname) ?></td>
                            </tr>
                        <?php endif; ?>
                        <!-- Nachname -->
                        <tr>
                            <th><i class="fas fa-shirt"></i></th>
                            <td><?= Html::encode($spieler->name) ?></td>
                        </tr>
                        <!-- Vollständiger Name -->
                        <tr>
                            <th><i class="fas fa-address-card"></i></th>
                            <td><?= Html::encode($spieler->fullname) ?></td>
                        </tr>
                        <!-- Geburtstag -->
                        <?php if ($spieler->geburtstag): ?>
                            <tr>
                                <th><i class="fas fa-birthday-cake"></i></th>
                                <td><?= Yii::$app->formatter->asDate($spieler->geburtstag, 'dd.MM.yyyy') ?></td>
                            </tr>
                        <?php endif; ?>
                        <!-- Geburtsort -->
                        <?php if ($spieler->geburtsort || $spieler->geburtsland): ?>
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i></th>
                                <td>
                                    <?= Html::encode($spieler->geburtsort) ?>
                                    <?php if ($spieler->geburtsland): ?>
                                        <?= Html::img(Helper::getFlagUrl($spieler->geburtsland, $spieler->geburtstag), ['alt' => $spieler->geburtsland, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <!-- Nationalitäten -->
                        <?php if ($spieler->nati1 || $spieler->nati2 || $spieler->nati3): ?>
                            <tr>
                                <th><i class="fas fa-flag"></i></th>
                                <td>
                                    <?php foreach ([$spieler->nati1, $spieler->nati2, $spieler->nati3] as $nation): ?>
                                        <?php if ($nation): ?>
                                            <?= Html::img(Helper::getFlagUrl($nation), ['alt' => $nation, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <!-- Größe -->
                        <?php if ($spieler->height): ?>
                            <tr>
                                <th><i class="fas fa-ruler-vertical"></i></th>
                                <td><?= Html::encode($spieler->height) ?> cm</td>
                            </tr>
                        <?php endif; ?>
                        <!-- Gewicht -->
                        <?php if ($spieler->weight): ?>
                            <tr>
                                <th><i class="fas fa-weight-hanging"></i></th>
                                <td><?= Html::encode($spieler->weight) ?> kg</td>
                            </tr>
                        <?php endif; ?>
                        <!-- Spielfuß -->
                        <?php if ($spieler->spielfuss): ?>
                            <tr>
                                <th><i class="fas fa-shoe-prints"></i></th>
                                <td><?= Html::encode($spieler->spielfuss) ?></td>
                            </tr>
                        <?php endif; ?>
                        <!-- Homepage -->
                        <?php if ($spieler->homepage): ?>
                            <tr>
                                <th><i class="fas fa-laptop-code"></i></th>
                                <td><?= Html::a($spieler->homepage, 'http://' . $spieler->homepage, ['target' => '_blank']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <!-- Facebook -->
                        <?php if ($spieler->facebook): ?>
                            <tr>
                                <th><i class="fas fa-facebook"></i></th>
                                <td><?= Html::a($spieler->facebook, 'http://www.facebook.com/' . $spieler->facebook, ['target' => '_blank']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <!-- Instagram -->
                        <?php if ($spieler->instagram): ?>
                            <tr>
                                <th><i class="fas fa-instagram"></i></th>
                                <td><?= Html::a($spieler->instagram, 'http://www.instagram.com/' . $spieler->instagram, ['target' => '_blank']) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Widget 2: Vereinskarriere -->
    <?php
    $currentMonth = date('Ym'); // Aktueller Monat im Format 'YYYYMM'
    ?>
    <?php if (!empty($vereinsKarriere)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Vereinskarriere</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th colspan="2">Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vereinsKarriere as $karriere): ?>
                                    <tr>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($karriere->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?></td>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($karriere->verein->id), ['alt' => $karriere->verein->name, 'style' => 'height: 30px;']) ?></td>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>text-align: left;"><?= Html::a(Html::encode($karriere->verein->name), ['/club/view', 'id' => $karriere->verein->id], ['class' => 'text-decoration-none']) ?></td>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::img(Helper::getFlagUrl($karriere->verein->land), ['alt' => $karriere->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                                        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>"><?= Html::encode($karriere->position->positionKurz) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
                              
    <!-- Widget 3: Jugendvereine -->
    <?php if (!empty($jugendvereine)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Jugendvereine</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th colspan="2">Verein</th>
                                    <th>Land</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jugendvereine as $jugend): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            if ($jugend->von || $jugend->bis) { // Nur ausgeben, wenn mindestens einer der Werte vorhanden ist
                                                if ($jugend->von && $jugend->bis) {
                                                    // Beide Werte vorhanden: Ausgabe von und bis
                                                    $von = DateTime::createFromFormat('Ym', $jugend->von)->format('Y');
                                                    $bis = DateTime::createFromFormat('Ym', $jugend->bis)->format('Y');
                                                    echo Html::encode($von === $bis ? $von : "$von - $bis");
                                                } elseif ($jugend->bis) {
                                                    // Nur bis vorhanden: Ausgabe nur bis
                                                    echo Html::encode(DateTime::createFromFormat('Ym', $jugend->bis)->format('Y'));
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td style="width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($jugend->verein->id), ['alt' => $jugend->verein->name, 'style' => 'height: 30px;']) ?></td>
                                        <td style="text-align: left;"><?= Html::encode($jugend->verein->name) ?></td>
                                        <td><?= Html::img(Helper::getFlagUrl($jugend->verein->land), ['alt' => $jugend->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                                        <td><?= Html::encode($jugend->position->positionKurz) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Widget 4: Nationalmannschaftskarriere -->
    <?php if (!empty($laenderspiele)): ?>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Nationalmannschaftskarriere</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Wettbewerb</th>
                                    <th colspan="3">Nation</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laenderspiele as $spiel): ?>
                                    <tr>
                                        <td><?= Html::encode($spiel->wettbewerb->name) ?> <?= Html::encode($spiel->jahr) ?></td>
                                        <td style="width: 35px; text-align: right;"><?= Html::img(Helper::getClubLogoUrl($spiel->landID), ['alt' => Helper::getClubName($spiel->landID), 'style' => 'height: 30px;']) ?></td>
                                        <td style="text-align: left;"><?= Html::a(Html::encode(Helper::getClubName($spiel->landID)), ['/club/view', 'id' => $spiel->landID], ['class' => 'text-decoration-none']) ?></td>
                                        <td><?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->landID)), ['alt' => Helper::getClubName($spiel->landID), 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                                        <td><?= Html::encode($spiel->position->positionKurz) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
