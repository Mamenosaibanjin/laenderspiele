<?php
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\Helper;

/** @var yii\data\ActiveDataProvider $spielerProvider */
/** @var yii\data\ActiveDataProvider $vereineProvider */
/** @var yii\data\ActiveDataProvider $stadienProvider */
/** @var yii\data\ActiveDataProvider $schirisProvider */
/** @var string $query */

$this->title = "Suche: " . Html::encode($query);

function renderBlock($title, $dataProvider, $linkRoute, $blockId) {
    $models = $dataProvider->getModels();
    if (empty($models)) return;
    
    // Icon Mapping (Material Icons)
    $icons = [
        'Spieler' => 'person',
        'Vereine' => 'groups',
        'Stadien' => 'stadium',
        'Schiedsrichter' => 'gavel',
    ];
    $materialIcon = $icons[$title] ?? 'search';
    
    Pjax::begin([
        'id' => 'pjax-' . $blockId,
        'enablePushState' => false,
        'timeout' => 5000,
        'linkSelector' => "#pjax-$blockId .pagination a",
    ]);
    
    echo "<div id='$blockId' class='mb-5 spielinfo-box'>";
    echo "<h4 class='mb-3'><span class='material-icons align-middle me-1'>$materialIcon</span>$title</h4>";
    
    echo "<div class='row'>";
    foreach (array_chunk($models, 5) as $colItems) {
        echo "<div class='col-md-6'>";
        foreach ($colItems as $item) {
            $icon = Helper::getFlagInfo($item['land'], null, false);
            $url = Url::to([$linkRoute, 'id' => $item['id']]);
            
            switch ($title) {
                case 'Spieler':
                    $vorname = $item['vorname'] ?? '';
                    $name = $item['name'] ?? '';
                    echo "<p>$icon " . Html::a(Html::encode($vorname . " " . $name), $url,
                        ['class' => 'text-decoration-none']) . "</p>";
                        break;
                        
                case 'Vereine':
                    $anzeige = Html::encode($item['name']);
                    $ort = !empty($item['ort']) ? " (" . Html::encode($item['ort']) . ")" : '';
                    echo "<p>$icon " . Html::a($anzeige, $url,
                        ['class' => 'text-decoration-none']) . $ort . "</p>";
                        break;
                        
                case 'Stadien':
                    $anzeige = Html::encode($item['name']);
                    $ort = !empty($item['stadt']) ? " – " . Html::encode($item['stadt']) : '';
                    echo "<p>$icon " . Html::a($anzeige, $url,
                        ['class' => 'text-decoration-none']) . $ort . "</p>";
                        break;
                        
                case 'Schiedsrichter':
                    $vorname = $item['vorname'] ?? '';
                    $name = $item['name'] ?? '';
                    echo "<p>$icon " . Html::a(Html::encode($vorname . " " . $name), $url,
                        ['class' => 'text-decoration-none']) . "</p>";
                        break;
                        
                default:
                    echo "<p>$icon " . Html::a(Html::encode($item['name']), $url,
                    ['class' => 'text-decoration-none']) . "</p>";
            }
        }
        echo "</div>";
    }
    echo "</div>";
    
    echo "<div class='mt-3'>";
    echo LinkPager::widget([
        'pagination' => $dataProvider->getPagination(),
        'options' => ['class' => 'pagination justify-content-center'],
    ]);
    echo "</div>";
    
    echo "</div>";
    Pjax::end();
}
?>

<div class="verein-page row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="card-body">

                <?= renderBlock('Spieler', $spielerProvider, 'spieler/view', 'spieler') ?>
                <?= renderBlock('Vereine', $vereineProvider, 'club/view', 'vereine') ?>
                <?= renderBlock('Stadien', $stadienProvider, 'stadium/view', 'stadien') ?>
                <?= renderBlock('Schiedsrichter', $schirisProvider, 'referee/view', 'schiris') ?>

            </div>
        </div>
    </div>
</div>
