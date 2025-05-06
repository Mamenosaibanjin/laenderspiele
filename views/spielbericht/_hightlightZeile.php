<?php
/** @var $aktion app\models\Games */
/** @var $spiel app\models\Spiel */
/** @var $previousScore array */

use yii\helpers\Html;
use app\components\Helper;

$minute = $aktion->minute;
$zusatz = $aktion->zusatz;
$currentScore = explode(':', $zusatz);
$spielerName = ' ';
$team = ' ';

if (!$aktion->spieler) {
    if ($aktion->aktion === 'ET') {
        $team = ($currentScore[0] > $previousScore[0]) ? 'auswärts' : 'heim';
    } else {
        $team = ($currentScore[0] > $previousScore[0]) ? 'heim' : 'auswärts';
    }
    $spielerName = 'unbekannt';
} else {
    $team = $spiel->isAuswaertsAktion($aktion->spieler->id) ? 'auswärts' : 'heim';
    $spielerName = Html::encode(
        ($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name
        );
}

$actionSvg = Helper::getActionSvg($aktion->aktion);
$relevanteAktionen = ['TOR', '11m', 'ET', 'RK'];
?>

<?php if (in_array($aktion->aktion, $relevanteAktionen)) : ?>
    <div class="highlight-row">
        <div class="heimname">
            <?= ($team === 'heim') ? $spielerName : ' ' ?>
        </div>
        <div class="heim">
            <?= ($team === 'heim') ? Html::encode($zusatz) : ' ' ?>
        </div>
        <div class="heim">
            <?= ($team === 'heim') ? $actionSvg : ' ' ?>
        </div>
        <div class="minute">
            <?= Html::encode($minute) < 200 ? Html::encode($minute) . '.' : ' ' ?>
        </div>
        <div class="auswaerts">
            <?= ($team === 'auswärts') ? $actionSvg : ' ' ?>
        </div>
        <div class="auswaerts">
            <?= ($team === 'auswärts') ? Html::encode($zusatz) : ' ' ?>
        </div>
        <div class="auswaertsname">
            <?= ($team === 'auswärts') ? $spielerName : ' ' ?>
        </div>
    </div>
<?php endif; ?>
