<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use app\components\Helper;
use app\components\SpielerHelper;
use app\models\SpielerLandWettbewerb;
use yii\helpers\Url;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */
/** @var yii\data\ActiveDataProvider $dataProvider */


$this->title = "Spieler - $turniername $jahr";

?>
<div class="verein-page row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Spieler") ?>
                </h3>
            </div>
            <div class="card-body">
                    <?php 
                    Pjax::begin([
                        'id' => 'spieler-grid',
                        'timeout' => 5000,
                        'enablePushState' => true, // URL ändert sich mit
                        'formSelector' => '#filter-form', // Wichtig für automatisches PJAX-Submit
                    ]); ?>

					<div class="filter-box-spieler mb-3">
    <form id="filter-form" method="get" action="" data-pjax="1">
        <div class="form-check form-check-inline">
            <?php foreach ($allePositionen as $position): ?>
                <label class="form-check-label mr-3">
                    <?= Html::checkbox('positionen[]', in_array($position->id, $selectedPositionen), [
                        'class' => 'positionen-filter',
                        'value' => $position->id,
                        'label' => $position->positionLang_de,
                    ]) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<?php
$spielerlisteUrl = Url::to(['projects/laenderspiele2.0/yii2-app-basic/web/turnier/spieler', 'tournamentID' => $tournamentID]);

$this->registerJs(new JsExpression(<<<JS
    // Bei jeder Änderung der Checkbox-Liste wird das Formular automatisch abgeschickt
    $('.positionen-filter').on('change', function () {
        let selected = $('.positionen-filter:checked').map(function () {
            return $(this).val();
        }).get();

        let sort = $('.grid-view th a.asc, .grid-view th a.desc').attr('href');
        let currentSort = '';

        if (sort) {
            let match = sort.match(/sort=([^&]+)/);
            if (match) {
                currentSort = match[1];
            }
        }

        let newUrl = '/projects/laenderspiele2.0/yii2-app-basic/web/turnier/{$tournamentID}/spieler';
        if (selected.length > 0) {
            newUrl += '/' + selected.join(',');
        } else {
            newUrl += '/';
        }

        newUrl += '/' + (currentSort || 'nach-name') + '/1'; // Seite immer zurück auf 1

        $.pjax.reload({
            container: '#spieler-grid',
            url: newUrl,
            replace: true,
        });
    });
JS));
?>

					
					<p>
						<strong>Gesamtanzahl:</strong> <?= $dataProvider->getTotalCount() ?> Spieler
                    
					<?php echo GridView::widget([
                        'id' => 'spieler-grid',
                        'dataProvider' => $dataProvider,
                        'layout' => '{items}{pager}', // Optional: {summary} für Zusammenfassung hinzufügen
                        'pager' => [
                            'firstPageLabel' => '«',
                            'lastPageLabel' => '»',
                            'prevPageLabel' => '‹',
                            'nextPageLabel' => '›',
                            'maxButtonCount' => 5,
                        ],
                        'columns' => [
                            [
                                'attribute' => 'nach-name',
                                'label' => 'Name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                return Html::a(
                                    Helper::getSpielerName($model->spielerID),
                                    ['/spieler/view', 'id' => $model->spielerID],
                                    ['class' => 'text-decoration-none']
                                    );
                                }
                            ],
                            [
                                'attribute' => 'nach-mannschaft',
                                'label' => 'Mannschaft',
                                'format' => 'raw',
                                'value' => function ($model) use ($tournamentID) {
                                $clubID = SpielerHelper::getNationId($model->spielerID, $tournamentID);
                                return Html::img(
                                    Helper::getClubLogoUrl($clubID),
                                    ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                    ) . Helper::getClubName($clubID);
                                }
                            ],
                            [
                                'attribute' => 'nach-geburtstag',
                                'label' => 'Geboren',
                                'value' => function ($model) {
                                return SpielerHelper::getBirthday($model->spielerID);
                                }
                            ],
                            [
                                'attribute' => 'nach-groesse',
                                'label' => 'Größe',
                                'value' => function ($model) {
                                return SpielerHelper::getHeight($model->spielerID);
                                }
                            ],
                            [
                                'attribute' => 'nach-position',
                                'label' => 'Position',
                                'value' => function ($model) use ($tournamentID) {
                                return SpielerHelper::getPosition($model->spielerID, $tournamentID);
                                }
                            ],
                        ],
                    ]);
                Pjax::end();
                ?>
            </div>
        </div>
    </div>

</div>
            