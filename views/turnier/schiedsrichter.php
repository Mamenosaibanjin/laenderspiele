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
					           return ['data-schiedsrichter-id' => $model->id, 'class' => 'spiele-row'];
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
                                'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:rgb(247,216,123); height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>',
                                'encodeLabel' => false,
                                'value' => function ($model) use ($tournamentID) {
                                return $model->gk_count;
                                }
                            ],
                            [
                                'attribute' => 'nach-gelbrote-karten',
                                'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-sentry-element="Svg" data-sentry-component="TwoCards" data-sentry-source-file="icons.tsx" style="height: 15px;">
    	                			 <path fill="#F8D94D" d="M6.06 23.98c-.49.1-.89-.2-.99-.59C4.57 21.5.53 6.37.02 4.47c-.1-.49.2-.89.6-.99 0 0 18.912 16.02 18.91 15.95.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                			 <path fill="#C00" d="M.62 3.48C1.9 3.14 12.2.37 13.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82C19.53 19.587.62 3.48.62 3.48z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                		</svg>',
                                'encodeLabel' => false,
                                'value' => function ($model) use ($tournamentID) {
                                return $model->grk_count;
                                }
                            ],
                            [
                                'attribute' => 'nach-rote-karten',
                                'label' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:#CC0000; height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>',
                                'encodeLabel' => false,
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
    // Nur sichtbare .spiele-row zählen
    var visibleRows = $('#schiedsrichter-grid table tbody tr.spiele-row:visible');
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
                let newRow = '<tr id="spiele-row-' + schiedsrichterId + '" class="spiele-row">' +
                             '<td colspan="' + colCount + '">' + response + '</td></tr>';
                \$row.after(newRow);
                refreshZebraStripes();
            },
            error: function () {
                let newRow = '<tr id="spiele-row-' + schiedsrichterId + '" class="spiele-row">' +
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
