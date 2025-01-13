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
    
}
?>