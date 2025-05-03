<?php

namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use app\models\Referee;
use Yii;
use yii\web\Response;

class RefereeController extends Controller
{
    public function actionView($id)
    {
        $isEditing = !(Yii::$app->user->isGuest); // Bearbeitungsmodus für eingeloggte Benutzer
        
        $referee = Referee::findOne($id);
        if (!$referee) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'Referee not found.'));
        }
        
        return $this->render('view', [
            'referee' => $referee,
            'isEditing' => $isEditing,
        ]);
    }
    
    public function actionNew()
    {
        $referee = new Referee();
        
        if ($referee->load(Yii::$app->request->post()) && $referee->save()) {
            return $this->redirect(['view', 'id' => $referee->id]);
        }
        
        return $this->render('view', [
            'referee' => $referee,
            'isEditing' => true, // Flag für Bearbeitungsmodus
        ]);
    }
    
}
?>