<?php
namespace app\controllers;

use yii\web\Controller;
use Yii;
use yii\db\Query;
use yii\data\ArrayDataProvider;

class SearchController extends Controller
{
    public function actionView($query = null)
    {
        if (!$query) {
            return $this->render('view', [
                'query' => null,
                'spielerProvider' => new ArrayDataProvider(['allModels' => []]),
                'vereineProvider' => new ArrayDataProvider(['allModels' => []]),
                'stadienProvider' => new ArrayDataProvider(['allModels' => []]),
                'schirisProvider' => new ArrayDataProvider(['allModels' => []]),
            ]);
        }
        
        // Abfrage für Spieler
        $spieler = (new Query())
        ->select(['id', 'vorname', 'name', 'nati1 as land'])
        ->from('spieler')
        ->where(['or',
            ['like', 'name', $query],
            ['like', 'fullname', $query],
            ['like', 'vorname', $query],
        ])
        ->all();
        
        // Abfrage für Vereine
        $clubs = (new Query())
        ->select(['id', 'name', 'ort', 'land'])
        ->from('clubs')
        ->where(['or',
            ['like', 'name', $query],
            ['like', 'namevoll', $query],
            ['like', 'ort', $query],
        ])
        ->andWhere(['!=', 'typID', 6])
        ->all();
        
        // Abfrage für Stadien
        $stadien = (new Query())
        ->select(['id', 'name', 'stadt', 'land'])
        ->from('stadiums')
        ->where(['or',
            ['like', 'name', $query],
            ['like', 'stadt', $query],
        ])
        ->all();
        
        // Abfrage für Schiedsrichter
        $schiris = (new Query())
        ->select(['id', 'vorname', 'name', 'nati1 as land'])
        ->from('referee')
        ->where(['or',
            ['like', 'name', $query],
            ['like', 'fullname', $query],
            ['like', 'vorname', $query],
        ])
        ->all();
        
        // Datenprovider mit Pagination
        $spielerProvider = new ArrayDataProvider([
            'allModels' => $spieler,
            'pagination' => [
                'pageSize' => 10,
                'pageParam' => 'spielerPage',
            ],
        ]);
        
        $vereineProvider = new ArrayDataProvider([
            'allModels' => $clubs,
            'pagination' => [
                'pageSize' => 10,
                'pageParam' => 'vereinePage',
            ],
        ]);
        
        $stadienProvider = new ArrayDataProvider([
            'allModels' => $stadien,
            'pagination' => [
                'pageSize' => 10,
                'pageParam' => 'stadienPage',
            ],
        ]);
        
        $schirisProvider = new ArrayDataProvider([
            'allModels' => $schiris,
            'pagination' => [
                'pageSize' => 10,
                'pageParam' => 'schirisPage',
            ],
        ]);
        
        return $this->render('view', [
            'query' => $query,
            'spielerProvider' => $spielerProvider,
            'vereineProvider' => $vereineProvider,
            'stadienProvider' => $stadienProvider,
            'schirisProvider' => $schirisProvider,
        ]);
    }
}
