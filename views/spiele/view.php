<?php
use yii\helpers\Html;
use app\components\Helper;

/** @var array $spiele */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Spiele - $turniername $jahr";
?>
<div class="card">
    <div class="card-header">
        <h3>
            Spiele - <?= Html::encode("$turniername - $jahr") ?>
        </h3>
    </div>
    <div class="card-body">
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
                        <td style="text-align: right;"><?= Html::encode($spiel->club1->name ?? 'Unbekannt') ?> <?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->club1->id)), ['alt' => $spiel->club1->name , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                        <td style="text-align: center;"><?= $spiel->getErgebnisHtml() ?></td>
                        <td><?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->club2->id)), ['alt' => $spiel->club2->name , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?> <?= Html::encode($spiel->club2->name ?? 'Unbekannt') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
