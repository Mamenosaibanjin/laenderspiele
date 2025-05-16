<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use app\components\Helper;
use app\components\SpielerHelper;
use app\components\StadiumHelper;
use app\models\SpielerLandWettbewerb;
use yii\helpers\Url;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */
/** @var yii\data\ActiveDataProvider $dataProvider */


$this->title = "Stadien - $turniername $jahr";

?>
<div class="verein-page row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Stadien") ?>
                </h3>
            </div>
            <div class="card-body">
                    <?php 
                    Pjax::begin([
                        'id' => 'stadien-grid',
                        'timeout' => 5000,
                        'enablePushState' => true, // URL ändert sich mit
                        'formSelector' => '#filter-form', // Wichtig für automatisches PJAX-Submit
                    ]); ?>
					
					<?php echo GridView::widget([
                        'id' => 'stadien-grid',
                        'dataProvider' => $dataProvider,
                        'layout' => '{items}{pager}', // Optional: {summary} für Zusammenfassung hinzufügen
                        'pager' => [
                            'firstPageLabel' => '«',
                            'lastPageLabel' => '»',
                            'prevPageLabel' => '‹',
                            'nextPageLabel' => '›',
                            'maxButtonCount' => 5,
                        ],
					    'rowOptions' => function ($model, $key, $index, $grid) {
					           return ['data-stadion-id' => $model->id, 'class' => 'stadion-row'];
					    },
                        'columns' => [
                            [
                                'attribute' => 'nach-name',
                                'label' => 'Name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                return Html::a($model->name, ['/stadion/' .$model->id], ['class' => 'text-decoration-none']);
                                }
                            ],
                            [
                                'attribute' => 'nach-stadt',
                                'label' => 'Stadt',
                                'format' => 'raw',
                                'value' => function ($model) use ($tournamentID) {
                                 return $model->stadt;
                                }
                            ],
                            [
                                'attribute' => 'nach-land',
                                'label' => 'Land',
                                'format' => 'raw',
                                'value' => function ($model) use ($tournamentID)  {
                                return Helper::getFlagInfo($model->land, Helper::getTurnierStartdatum($tournamentID), true);
                                }
                            ],
                            [
                                'attribute' => 'nach-kapazitaet',
                                'label' => 'Kapazität',
                                'format' => ['decimal', 0],
                                'value' => function ($model) use ($tournamentID) {
                                return $model->kapazitaet;
                                }
                            ],
                            [
                                'class' => 'yii\grid\Column',
                                'header' => '',
                                'content' => function($model) use ($tournamentID) {
                                    $url = Url::to(['turnier/spiele-im-stadion', 'stadionID' => $model->id, 'tournamentID' => $tournamentID]);
                                    return Html::a('Zeige alle Spiele', 'javascript:void(0);', [
                                       'class' => 'toggle-spiele btn btn-sm btn-turnier',
                                        'data-stadion-id' => $model->id,
                                        'data-tournament-id' => $tournamentID,
                                        'data-url' => $url
                                    ]);
                                }
                            ]
                        ],
                    ]);
                Pjax::end();
                ?>
            </div>
        </div>
    </div>

</div>
<?php             
$js = <<<JS
function refreshZebraStripes() {
    // Nur sichtbare .stadion-row zählen
    var visibleRows = $('#stadien-grid table tbody tr.stadion-row:visible');
    visibleRows.each(function(index) {
        // Entferne alte Farben
        $(this).removeClass('table-even table-odd');
        // Füge neue Klasse basierend auf Index hinzu
        if (index % 2 === 0) {
            $(this).css('background-color', '#f2f2f2');
        } else {
            $(this).css('background-color', '#ffffff');
        }
        // Auch die ggf. darunter liegende spiele-row gleich einfärben (optional)
        let next = $(this).next();
        if (next.hasClass('spiele-row')) {
            next.css('background-color', $(this).css('background-color'));
        }
    });
}

$(document).on('click', '.toggle-spiele', function() {
    let stadionId = $(this).data('stadion-id');
    let url = $(this).data('url');
    let \$row = $(this).closest('tr');

    let \$existingRow = $('#spiele-row-' + stadionId);

    if (\$existingRow.length) {
        \$existingRow.remove(); // löschen statt toggle()
    } else {
        // Neue Zeile einfügen
        let colCount = \$row.find('td').length;
        let tournamentId = $(this).data('tournament-id');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                let newRow = '<tr id="spiele-row-' + stadionId + '" class="spiele-row">' +
                             '<td colspan="' + colCount + '">' + response + '</td></tr>';
                \$row.after(newRow);
                refreshZebraStripes();
            },
            error: function () {
                let newRow = '<tr id="spiele-row-' + stadionId + '" class="spiele-row">' +
                             '<td colspan="' + colCount + '"><div class="text-danger">Fehler beim Laden der Spiele.</div></td></tr>';
                \$row.after(newRow);
            }
        });
    }

    // Zebra-Streifen neu anwenden
    refreshZebraStripes();
});
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>
