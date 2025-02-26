<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use app\models\Spieler;
use app\models\SpielerLandWettbewerb;

class SpielerLandWettbewerbController extends Controller
{
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $spielerID = Yii::$app->request->post('spielerID');
        $landID = Yii::$app->request->post('landID');
        $tournamentID = Yii::$app->request->post('tournamentID');
        
        if (!$spielerID || !$landID) {
            return ['success' => false, 'message' => 'Fehlende Parameter.'];
        }
        
        $model = new SpielerLandWettbewerb();
        $model->spielerID = $spielerID;
        $model->landID = $landID;
        $model->tournamentID = $tournamentID ?: null; // Falls null, bleibt es null
        
        if ($model->save()) {
            return ['success' => true, 'message' => 'Spieler hinzugefügt!'];
        } else {
            return ['success' => false, 'message' => 'Fehler beim Speichern.'];
        }
    }
    
    public function actionAddPlayerToSquad()
    {
        $model = new SpielerLandWettbewerb();
        $model->spielerID = Yii::$app->request->post('spielerID');
        $model->landID = Yii::$app->request->post('landID');
        $model->turnierID = Yii::$app->request->post('turnierID');
        
        if ($model->validate() && $model->save()) {
            Yii::$app->session->setFlash('success', 'Spieler erfolgreich hinzugefügt!');
            return $this->redirect(['kader/view', 'id' => $model->landID, 'year' => date('Y')]);
        } else {
            Yii::$app->session->setFlash('error', 'Fehler beim Hinzufügen des Spielers.');
            return $this->redirect(['kader/view', 'id' => $model->landID, 'year' => date('Y')]);
        }
    }
    
    public function actionSearch()
    {
        $query = Yii::$app->request->get('query');
        $players = Spieler::find()
        ->where(['like', 'name', $query])
        ->all();
        
        $results = [];
        foreach ($players as $player) {
            $results[] = [
                'id' => $player->id,
                'name' => $player->name,
            ];
        }
        
        return $this->asJson($results);
    }
    
}
