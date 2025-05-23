<?php

namespace app\controllers;

use yii\web\Controller;
use app\components\SquadHelper;
use app\models\Club;
use app\models\Nation;
use app\models\Stadion;
use Yii;
use yii\web\Response;

class ClubController extends Controller
{
    public function actionView($id)
    {
        $isEditing = !(Yii::$app->user->isGuest); // Bearbeitungsmodus für eingeloggte Benutzer
        
        // Wenn eine ID vorhanden ist, bestehenden Club laden, sonst neues Modell erstellen
        $club = $id ? Club::findOne($id) : new Club();
        
        if (!$club && $id) {
            throw new \yii\web\NotFoundHttpException('Der angeforderte Club wurde nicht gefunden.');
        }
        
        // Weitere benötigte Daten laden
        $nation = $club->nation; // Annahme: Relation "nation" existiert
        $stadium = $club->stadion; // Annahme: Relation "stadium" existiert
        
        $currentYear = substr(SquadHelper::getLastSquadYear($club->id),0,4);
        
        // Spiele und Kader je nach TypID laden
        $recentMatches = in_array($club->typID, [1, 2]) ? $club->getRecentMatches() : null;
        $upcomingMatches = in_array($club->typID, [1, 2]) ? $club->getUpcomingMatches() : null;
        $squad = in_array($club->typID, [3, 5]) ? $club->getSquad($id, $currentYear) : null;
        $nationalSquad = in_array($club->typID, [1, 2]) ? $club->getNationalSquad($id) : null;
        
        // Bearbeitungsmodus: Daten speichern
        if ($isEditing && Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            
            // Club-Daten laden
            if ($club->load($request->post())) {
                // Farben speichern
                $farbenArray = $request->post('farben', []); // Array von Farbwerten
                $club->farben = implode('-', $farbenArray); // Speicherung als String
                // Nachfolger-ID speichern, wenn typID = 6
                if ($club->typID == 6) {
                    $clubData = $request->post('Club', []); // Hole die Daten aus dem Club-Array
                    
                    if (isset($clubData['nachfolgerID'])) {
                        $nachfolgerID = $clubData['nachfolgerID'];
                        echo "Nachfolger: " . $nachfolgerID; // Debug-Ausgabe
                        $club->nachfolgerID = $nachfolgerID ?: null;
                    } else {
                        $club->nachfolgerID = null; // Keine Nachfolger-ID gefunden
                    }
                } else {
                    $club->nachfolgerID = null; // Keine Nachfolger-ID bei anderen Typen
                }
                
                if ($club->save()) {
                    Yii::debug($club->attributes, __METHOD__);
                    Yii::$app->session->setFlash('success', 'Die Clubdaten wurden erfolgreich gespeichert.');
                    return $this->redirect(['club/view', 'id' => $club->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Fehler beim Speichern der Clubdaten.');
                }
            }
        }
        
        // View rendern
        return $this->render('view', [
            'club' => $club,
            'nation' => $nation,
            'stadium' => $stadium,
            'recentMatches' => $recentMatches,
            'upcomingMatches' => $upcomingMatches,
            'squad' => $squad,
            'nationalSquad' => $nationalSquad,
            'isEditing' => $isEditing,
            'stadien' => Stadion::getStadiums(),
            'vereine' => Club::getClubs(),
        ]);
    }

    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $term = Yii::$app->request->get('term'); // Das Suchfeld "term" wird von jQuery UI Autocomplete verwendet
        $clubs = Club::find()
        ->select(['id', 'name as value']) // 'value' ist erforderlich für jQuery UI
        ->where(['like', 'name', $term])
        ->asArray()
        ->all();
        
        return $clubs;
    }
    
    public function actionNew()
    {
        $club = new Club();
        
        if ($club->load(Yii::$app->request->post()) && $club->save()) {
            return $this->redirect(['view', 'id' => $club->id]);
        }
        
        return $this->render('view', [
            'club' => $club,
            'nation' => $club->nation,
            'stadium' => $club->stadion,
            'recentMatches' => [],
            'upcomingMatches' => [],
            'squad' => [],
            'nationalSquad' => [],
            'isEditing' => true,
            'stadien' => Stadion::getStadiums(),
            'vereine' => Club::getClubs(),
        ]);
    }

}
?>