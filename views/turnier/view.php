<?php
use yii\helpers\Html;
use app\components\Helper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Turnier - $turniername $jahr";
?>
<div class="verein-page row">
    <!-- Widget 1: Vereinsdaten -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Teilnehmer") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <?php foreach ($clubs as $index => $club): ?>
                            <tr>
                                <td style="width: 50%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::img(Helper::getFlagUrl($club['land']), [
                                        'alt' => $club['name'],
                                        'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
                                    ]) ?>
                                    <?= Html::encode($club['name']) ?>
                                </td>
                                <td style="width: 10%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('Info', ['/club/view', 'id' => $club['id']], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 10%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('Spiele', ['/spielplan/view', 'clubid' => $club['id'], 'jahr' => $jahr], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 30%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                <?php if ($club['spieleranzahl'] > 0): ?>
                                    <?= Html::a(
                                        "Kader",
                                        ['/kader/view', 'id' => $club['id'], 'year' => $jahr, 'turnier' => $wettbewerbID],
                                        ['class' => 'text-decoration-none']
                                    ) ?>
                                    <?= " ({$club['spieleranzahl']} Spieler)" ?>
                                <?php else: ?>
                                    ----- 
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

	<div class="col-md-1">&nbsp;</div>
    
    <!-- Widget 2: Zusammenfassung -->
	<div class="col-md-4">
		<div class="card">
			<div class="card-header"><h3>Übersicht</h3></div>
				<div class="card-body d-flex align-items-center">
                    <table class="table">
                         <tbody>
                            <?php 
                            $lastDate = null; // Variable für das letzte Datum
                            foreach ($spiele as $spiel): 
                                $currentDate = $spiel->getFormattedDate(); // Aktuelles Datum
                            ?>
                                <!-- Neue Zeile bei neuem Datum -->
                                <?php if ($lastDate !== $currentDate): ?>
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-left font-weight-bold">
                                            <?= Html::encode($currentDate) ?>
                                        </td>
                                    </tr>
                                    <?php $lastDate = $currentDate; // Aktualisiere das letzte Datum ?>
                                <?php endif; ?>
                                
                                <tr>
                                    <td style="width: 200px;"><?= $spiel->zeit ? Html::encode(Yii::$app->formatter->asTime($spiel->zeit, 'php:H:i')) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
 
                </div>
            </div>

        </div>
    </div>
            