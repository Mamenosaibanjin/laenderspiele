<?php

namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use app\models\Club;
use app\models\Nation;
use app\models\Spiel;
use app\models\Stadion;
use Yii;
use yii\web\Response;
use app\models\Stadiums;

class StadionController extends Controller
{
    public function actionView($id)
    {
        $isEditing = !(Yii::$app->user->isGuest); // Bearbeitungsmodus für eingeloggte Benutzer
        
        $stadium = Stadion::findOne($id);
        if (!$stadium) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'Stadium not found.'));
        }
        
        $teams = Club::find()
            ->where(['stadionID' => $id])
            ->andWhere(['<', 'typID', 6])
            ->all();

        $matches = new ActiveDataProvider([
            'query' => Spiel::find()
            ->joinWith(['turnier', 'club1', 'club2', 'wettbewerb'])
            ->where(['spiele.stadiumID' => $id])
            ->orderBy([
                'turnier.datum' => SORT_DESC,
                'spiele.id' => SORT_DESC, // Spiel-ID hinzufügen
            ])
            ->limit(10), // Limit auf 10 Einträge setzen
            'pagination' => false, // Pagination deaktivieren
        ]);
        
        return $this->render('view', [
            'stadium' => $stadium,
            'teams' => $teams,
            'matches' => $matches,
            'isEditing' => $isEditing,
        ]);
    }
    
    public function actionNew()
    {
        $stadium = new Stadion();
        
        if ($stadium->load(Yii::$app->request->post()) && $stadium->save()) {
            return $this->redirect(['view', 'id' => $stadium->id]);
        }
        
        return $this->render('view', [
            'stadium' => $stadium,
            'isEditing' => true, // Flag für Bearbeitungsmodus
            'teams' => [], // Keine Teams bei einem neuen Stadion
            'matches' => new ActiveDataProvider(['query' => Spiel::find()->where('0=1')]), // Leerer DataProvider
        ]);
    }
    
    
    public function actionGetList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $stadien = Stadion::find()->select(['id', 'name', 'stadt'])->asArray()->all();
        return array_map(function ($stadion) {
            return [
                'label' => $stadion['name'] . ', ' . $stadion['stadt'],
                'value' => $stadion['id'],
                'klarname' => $stadion['name']
            ];
        }, $stadien);
    }
    
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $term = Yii::$app->request->get('term');
        $stadium = Stadion::find()
        ->select(['id', 'CONCAT(name, \', \', stadt , \' (\', land , \')\')  as value']) // 'value' ist erforderlich für jQuery UI
        ->where(['like', 'name', $term])
        ->asArray()
        ->all();
        
        return $stadium;
    }
    
}
?>