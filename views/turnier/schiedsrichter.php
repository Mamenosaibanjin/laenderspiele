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


$this->title = "Schiedsrichter - $turniername $jahr";

?>
<div class="verein-page row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Schiedsrichter") ?>
                </h3>
            </div>
            <div class="card-body">
                    <?php 
                    Pjax::begin([
                        'id' => 'schiedsrichter-grid',
                        'timeout' => 5000,
                        'enablePushState' => true, // URL ändert sich mit
                        'formSelector' => '#filter-form', // Wichtig für automatisches PJAX-Submit
                    ]); ?>
					
					<?php echo GridView::widget([
                        'id' => 'schiedsrichter-grid',
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
					           return ['data-schiedsrichter-id' => $model->id, 'class' => 'schiedsrichter-row'];
					    },
                        'columns' => [
                            [
                                'attribute' => 'nach-name',
                                'label' => 'Name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                return Html::a($model->vorname . " " . $model->name, ['/schiedsrichter/' .$model->id], ['class' => 'text-decoration-none']);
                                }
                            ],
                            [
                                'attribute' => 'nach-geburtstag',
                                'label' => 'geboren',
                                'format' => 'raw',
                                'value' => function ($model) use ($tournamentID) {
                                 return Helper::getFormattedDate($model->geburtstag);
                                }
                            ],
                            [
                                'attribute' => 'nach-land',
                                'label' => 'Land',
                                'format' => 'raw',
                                'value' => function ($model) use ($tournamentID)  {
                                return Helper::getFlagInfo($model->nati1, Helper::getTurnierStartdatum($tournamentID), true);
                                }
                            ],
                            [
                                'attribute' => 'nach-spiele',
                                'label' => 'Sp.',
                                'value' => function ($model) use ($tournamentID) {
                                return $model->spiele;
                                }
                            ],
                            [
                                'attribute' => 'nach-gelbe-karten',
                                'label' => 'GK',
                                'value' => function ($model) use ($tournamentID) {
                                return $model->gk_count;
                                }
                            ],
                            [
                                'attribute' => 'nach-gelbrote-karten',
                                'label' => 'GRK',
                                'value' => function ($model) use ($tournamentID) {
                                return $model->grk_count;
                                }
                            ],
                            [
                                'attribute' => 'nach-rote-karten',
                                'label' => 'RK',
                                'value' => function ($model) use ($tournamentID) {
                                return $model->rk_count;
                                }
                            ],
                            [
                                'class' => 'yii\grid\Column',
                                'header' => '',
                                'content' => function($model) use ($tournamentID) {
                                    $url = Url::to(['turnier/schiedsrichter-spiele', 'refereeID' => $model->id, 'tournamentID' => $tournamentID]);
                                    return Html::a('Zeige alle Spiele', 'javascript:void(0);', [
                                       'class' => 'toggle-spiele btn btn-sm btn-turnier',
                                        'data-schiedsrichter-id' => $model->id,
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
    // Nur sichtbare .schiedsrichter-row zählen
    var visibleRows = $('#schiedsrichter-grid table tbody tr.schiedsrichter-row:visible');
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
    let schiedsrichterId = $(this).data('schiedsrichter-id');
    let url = $(this).data('url');
    let \$row = $(this).closest('tr');

    let \$existingRow = $('#spiele-row-' + schiedsrichterId);

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
                let newRow = '<tr id="schiedsrichter-row-' + schiedsrichterId + '" class="schiedsrichter-row">' +
                             '<td colspan="' + colCount + '">' + response + '</td></tr>';
                \$row.after(newRow);
                refreshZebraStripes();
            },
            error: function () {
                let newRow = '<tr id="schiedsrichter-row-' + schiedsrichterId + '" class="schiedsrichter-row">' +
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
