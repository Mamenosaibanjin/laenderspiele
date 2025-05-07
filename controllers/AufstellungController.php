<?php
namespace app\controllers;

use PHPUnit\Util\Json;
use app\models\Aufstellung;
use app\models\Spiel;
use Yii;
use yii\web\Controller;

class AufstellungController extends Controller
{
    public function actionSpielerSuche($spielID, $term)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return (new \yii\db\Query())
        ->select(['spieler.id', "CONCAT(spieler.vorname, ' ', spieler.name) AS value"])
        ->from('spieler')
        ->innerJoin('spieler_land_wettbewerb slw', 'slw.spielerID = spieler.id')
        ->innerJoin('tournament t', 'slw.tournamentID = t.id')
        ->innerJoin('turnier tu', 'tu.tournamentID = t.id')
        ->where(['tu.spielID' => $spielID])
        ->andFilterWhere([
            'or',
            ['like', 'spieler.name', $term],
            ['like', 'spieler.vorname', $term],
        ])
        ->limit(10)
        ->all();
    }
    
    public function actionSpielerAufstellungSuche($spielID, $clubID, $term)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return (new \yii\db\Query())
        ->select(['s.id', "CONCAT(s.vorname, ' ', s.name) AS value"])
        ->from('turnier t')
        ->innerJoin('spieler_land_wettbewerb slw', 'slw.tournamentID = t.tournamentID')
        ->innerJoin('spieler s', 's.id = slw.spielerID')
        ->where(['t.spielID' => $spielID])
        ->andWhere(['slw.landID' => $clubID])
        ->andWhere(['or',
            ['like', 's.name', $term],
            ['like', 's.vorname', $term],
        ])
        ->limit(10)
        ->all();
    }
    
    public function actionSpeichern()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $spielID = (int)($post['spielID'] ?? 0);
        if (!$spielID) {
            Yii::$app->session->setFlash('error', 'Keine gültige Spiel-ID');
            return $this->redirect(['spielbericht/view', 'id' => $spielID]);
        }
        
        $spiel = \app\models\Spiel::findOne($spielID);
        if (!$spiel) {
            Yii::$app->session->setFlash('error', 'Spiel nicht gefunden');
            return $this->redirect(['spielbericht/view', 'id' => $spielID]);
        }
        
        // Helferfunktion zum Speichern einer Aufstellung (DRY-Prinzip)
        $speichereAufstellung = function($spielerArray, $trainerID, $aufstellungsID = null) {
            $aufstellung = $aufstellungsID
            ? \app\models\Aufstellung::findOne($aufstellungsID)
            : new \app\models\Aufstellung();
            
            if (!$aufstellung) {
                $aufstellung = new \app\models\Aufstellung();
            }
            
            foreach ($spielerArray as $key => $id) {
                $attr = $key . 'ID';
                if ($aufstellung->hasAttribute($attr)) {
                    $aufstellung->$attr = $id ?: null;
                }
            }
            
            $aufstellung->coachID = $trainerID ?: null;
            
            if ($aufstellung->save()) {
                return $aufstellung->id;
            }
            
            Yii::$app->session->setFlash('error', 'Aufstellung konnte nicht gespeichert werden.');
            return $this->redirect(['spielbericht/view', 'id' => $spielID]);
        };
        
        try {
            // Heimmannschaft
            $spielerH = $post['spielerH'] ?? [];
            $trainerH = $post['trainerH'] ?? null;
            $clubID_H = (int)($post['clubID-H'] ?? 0);
            $aufstellung1ID = $spiel->aufstellung1ID;
            
            $neueAufstellung1ID = $speichereAufstellung($spielerH, $trainerH, $aufstellung1ID);
            
            // Auswärtsmannschaft
            $spielerA = $post['spielerA'] ?? [];
            $trainerA = $post['trainerA'] ?? null;
            $clubID_A = (int)($post['clubID-A'] ?? 0);
            $aufstellung2ID = $spiel->aufstellung2ID;
            
            $neueAufstellung2ID = $speichereAufstellung($spielerA, $trainerA, $aufstellung2ID);
            
            // Aufstellungs-IDs im Spielmodell aktualisieren, falls neu
            $spiel->aufstellung1ID = $neueAufstellung1ID;
            $spiel->aufstellung2ID = $neueAufstellung2ID;
            $spiel->save(false);
            
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->session->setFlash('success', 'Aufstellungen wurden gespeichert.');
            return $this->redirect(['spielbericht/view', 'id' => $spielID]);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['spielbericht/view', 'id' => $spielID]);
        }
    }
    
    
}