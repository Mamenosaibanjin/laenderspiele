<?php

namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use app\models\Club;
use app\models\Nation;
use app\models\Spiel;
use app\models\Stadiums;
use Yii;
use yii\web\Response;

class StadionController extends Controller
{
    public function actionView($id)
    {
        $stadium = Stadiums::findOne($id);
        if (!$stadium) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'Stadium not found.'));
        }
        
        $teams = Club::find()->where(['stadionID' => $id])->all();
        $matches = new ActiveDataProvider([
            'query' => Spiel::find()
            ->joinWith(['turnier', 'club1', 'club2', 'wettbewerb'])
            ->where(['spiele.stadiumID' => $id])
            ->orderBy(['turnier.datum' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);
        
        $isEditing = !Yii::$app->user->isGuest && Yii::$app->user->can('updateStadium');
        
        return $this->render('view', [
            'stadium' => $stadium,
            'teams' => $teams,
            'matches' => $matches,
            'isEditing' => $isEditing,
        ]);
    }
    
}
?>