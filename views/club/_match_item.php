<?php
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use app\components\Helper;

/* @var $recentMatches array */
/* @var $club app\models\Club */

$dataProvider = new ArrayDataProvider([
    'allModels' => $recentMatches,
    'pagination' => ['pageSize' => 5],
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false, // Entfernt die "Showing X of Y"-Zeile
    'tableOptions' => ['class' => 'table table-striped table-bordered'], // Optional: Bootstrap-Stile
    'columns' => [
        [
            'attribute' => 'turnier.datum',
            'label' => 'Datum',
            'value' => function ($model) {
                return Yii::$app->formatter->asDate($model->turnier->datum, 'php:d.m.Y');
            },
        ],
        [
            'attribute' => 'turnier.zeit',
            'label' => 'Uhrzeit',
            'value' => function ($model) {
                return Yii::$app->formatter->asTime($model->turnier->zeit, 'php:H:i');
            },
        ],
        [
            'label' => 'H/A',
            'value' => function ($model) use ($club) {
                return $model->club1ID == $club->id ? 'H' : 'A';
            },
        ],
        [
            'label' => 'Gegner',
            'value' => function ($model) use ($club) {
                return $model->club1ID == $club->id ? $model->club2->name : $model->club1->name;
            },
        ],
        [
            'label' => 'Ergebnis',
            'format' => 'html', // HTML erlaubt farbige Markierung
            'value' => function ($model) use ($club) {
                $isHome = $model->club1ID == $club->id;
                $resultColor = Helper::getResultColor($isHome, $model);
                $score = $isHome
                    ? Html::encode($model->tore1) . ':' . Html::encode($model->tore2)
                    : Html::encode($model->tore2) . ':' . Html::encode($model->tore1);
                return Html::tag('strong', $score, ['class' => $resultColor]);
            },
        ],
    ],
]);
?>