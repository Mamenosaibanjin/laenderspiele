<?php
namespace app\controllers;

use PHPUnit\Util\Json;
use app\models\Aufstellung;
use app\models\Spiel;
use Yii;
use yii\web\Controller;

class AufstellungController extends Controller
{
    public function actionSpielerSuche($spielID, $clubID, $term)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return (new \yii\db\Query())
        ->select(['spieler.id', "CONCAT(spieler.vorname, ' ', spieler.name) AS name"])
        ->from('spieler')
        ->innerJoin('spieler_land_wettbewerb slw', 'slw.spielerID = spieler.id')
        ->innerJoin('tournament t', 'slw.tournamentID = t.id')
        ->innerJoin('turnier tu', 'tu.tournamentID = t.id')
        ->where(['tu.spielID' => $spielID])
        ->andWhere(['slw.landID' => $clubID])
        ->andFilterWhere([
            'or',
            ['like', 'spieler.name', $term],
            ['like', 'spieler.vorname', $term],
        ])
        ->limit(10)
        ->all();
    }
    
    public function actionSpeichern()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $request = json_decode(Yii::$app->request->getRawBody(), true);
        
        if (!$request) {
            return ['success' => false, 'message' => 'Ungültiger JSON-Body.'];
        }
        
        $spielID = (int)($request['spielID'] ?? 0);
        $type = $request['type'] ?? '';
        $spieler = $request['spieler'] ?? [];
        $trainerID = $request['trainer'] ?? null;
        
        // Hole das Spiel anhand der ID
        $spiel = \app\models\Spiel::findOne($spielID);
        if (!$spiel) {
            return ['success' => false, 'message' => 'Spiel nicht gefunden.'];
        }
        
        // Hole die bestehende Aufstellung anhand des Types
        if ($type === 'H') {
            $aufstellung = $spiel->aufstellung1ID ? \app\models\Aufstellung::findOne($spiel->aufstellung1ID) : null;
        } elseif ($type === 'A') {
            $aufstellung = $spiel->aufstellung2ID ? \app\models\Aufstellung::findOne($spiel->aufstellung2ID) : null;
        } else {
            return ['success' => false, 'message' => 'Ungültiger Typ (nur H oder A erlaubt).'];
        }
        
        // Wenn keine vorhanden, neue anlegen
        if (!$aufstellung) {
            $aufstellung = new \app\models\Aufstellung();
        }
        
        foreach ($spieler as $key => $id) {
            $attr = $key . 'ID'; // z.B. aus "spieler1" wird "spieler1ID"
            if ($aufstellung->hasAttribute($attr)) {
                $aufstellung->$attr = $id ?: null;
            }
        }
        $aufstellung->coachID = $trainerID ?: null;
        
        if ($aufstellung->save()) {
            // Aufstellungs-ID im Spiel aktualisieren
            if ($type === 'H') $spiel->aufstellung1ID = $aufstellung->id;
            else if ($type === 'A') $spiel->aufstellung2ID = $aufstellung->id;
            $spiel->save(false);
            
            return ['success' => true];
        }
        
        return ['success' => false, 'errors' => $aufstellung->errors];
    }
    
}