<?php
namespace app\components;

use yii\helpers\Html;
use Yii;

class GameHelper
{
    /**
     * Erstellt ein Widget mit einer Anzahl von Spielen.
     *
     * @param string $title - Überschrift des Widgets
     * @param array $games - Liste der Spiele.
     * @param object $club - der relevanta Club
     * @param string $emptyMessage - Fallback-Text für keine Spiele
     * @return array - Gefilterte und sortierte Spiele.
     */
    public static function renderMatchWidget($title, $matches, $club, $emptyMessage)
    {
        return $matches
        ? \yii\grid\GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $matches,
                'pagination' => ['pageSize' => 5],
            ]),
            'summary' => false, // Entfernt die Zusammenfassung ("Showing X of Y")
            'columns' => [
                [
                    'attribute' => 'datum',
                    'label' => Yii::t('app', 'Date'),
                    'value' => function ($model) {
                    return Helper::getFormattedDate($model->turnier->datum);
                    },
                    ],
                [
                    'attribute' => 'zeit',
                    'label' => Yii::t('app', 'Time'),
                    'value' => function ($model) {
                    return Helper::getFormattedTime($model->turnier->zeit);
                    },
                    ],
                [
                    'label' => Yii::t('app', 'Home') . '/' . Yii::t('app', 'Away'),
                    'value' => function ($model) use ($club) {
                    return $model->club1ID == $club->id ? Yii::t('app', 'H') : Yii::t('app', 'A');
                    },
                    ],
                [
                    'label' => Yii::t('app', 'Opponent'),
                    'value' => function ($model) use ($club) {
                    return Helper::getLocalizedOpponent($model, $club);
                    },
                    ],
                [
                    'label' => Yii::t('app', 'Result'),
                    'format' => 'html',
                    'value' => function ($model) use ($club) {
                    $isHome = $model->club1ID == $club->id;
                    $resultColor = Helper::getResultColor($isHome, $model);
                    
                    $result = $isHome
                    ? Html::encode($model->tore1) . ':' . Html::encode($model->tore2)
                    : Html::encode($model->tore2) . ':' . Html::encode($model->tore1);
                    
                    return Html::tag('strong', $result, ['class' => $resultColor]);
                    },
                    ],
                ],
                'tableOptions' => ['class' => 'table table-striped table-bordered'], // Bootstrap Tabelle
                ])
            : "<p>$emptyMessage</p>";
    }
}
?>