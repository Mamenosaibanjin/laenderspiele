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
                    return GameHelper::getLocalizedOpponent($model, $club);
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
        
    public static function getLocalizedOpponent($model, $club)
    {
        $opponentClub = $model->club1ID == $club->id ? $model->club2 : $model->club1;
        
        return ClubHelper::getLocalizedName($opponentClub, $opponentClub->name);
    }
    
    public static function getActionLogos($spielerID, $spielID, $isEingewechselt = false)
    {
        $aktionen = \app\models\Games::find()
        ->where(['spielID' => $spielID])
        ->andWhere(['or',
            ['spielerID' => $spielerID],
            $isEingewechselt ? ['spieler2ID' => $spielerID, 'aktion' => 'AUS'] : '0=1'
        ])
        ->andWhere(['<', 'minute', 200])
        ->orderBy('minute')
        ->all();
        
        $actionGroups = [];
        
        foreach ($aktionen as $aktion) {
            $key = $aktion->aktion;
            $minute = ($aktion->minute > 0) ? $aktion->minute : '';
            
            if (!isset($actionGroups[$key])) {
                $actionGroups[$key] = [];
            }
            
            $actionGroups[$key][] = $minute;
        }
        
        $output = '';
            
        foreach ($actionGroups as $aktion => $minuten) {
            $icon = ($isEingewechselt AND $aktion == 'AUS') ? Helper::getActionSvg('EIN') : Helper::getActionSvg($aktion);
            $hochzahl = '';
            
            // Nur für Tore/ETOR/ELF eine hochgestellte Anzahl
            $hochzahl = '';
            if (in_array($aktion, ['TOR', 'ET', '11m']) && count($minuten) > 1) {
                $hochzahl = '<span class="action-count-circle">' . count($minuten) . '</span>';
            }
            
            $output .= $icon . $hochzahl;
            $output .= ' <small style="font-size: 0.6rem;"><sup>' . implode(', ', $minuten) . '\'</sup></small> ';
        }
        
        return $output;
    }
    
}
?>