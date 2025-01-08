<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Club;
use app\models\Nation;
use app\models\Stadiums;
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
        
        // Weitere benötigte Daten laden (z. B. Nation, Stadion, Spiele, Kader)
        $nation = $club->nation; // Annahme: Relation "nation" existiert
        $stadium = $club->stadion; // Annahme: Relation "stadium" existiert
        
        // Daten für Widgets basierend auf `typID`
        // Spiele und Kader nur laden, wenn die TypID zutrifft
        $recentMatches = in_array($club->typID, [1, 2]) ? $club->getRecentMatches() : null;
        $upcomingMatches = in_array($club->typID, [1, 2]) ? $club->getUpcomingMatches() : null;
        
        // Kader nur laden, wenn TypID 3 oder 5
        $squad = in_array($club->typID, [3, 5]) ? $club->getSquad($id) : null;
        
        // National-Kader laden (Prüfe, ob diese Logik zutrifft)
        $nationalSquad = in_array($club->typID, [1, 2]) ? $club->getNationalSquad($id) : null;
        
        // Speichern der Daten, wenn es sich um einen Bearbeitungsmodus handelt
        if ($isEditing && $club->load(Yii::$app->request->post()) && $club->save()) {
            Yii::debug($club->attributes, __METHOD__);
            
            Yii::$app->session->setFlash('success', 'Die Clubdaten wurden erfolgreich gespeichert.');
            return $this->redirect(['club/view', 'id' => $club->id]);
        }
        
        // Zusätzliche Daten für die Ansicht
        $nationen = Nation::find()
        ->select(['kuerzel', 'land_de'])
        ->from('nation')
        ->where(['not', ['ISO3166' => null]]) // Nur Nationen mit gültigen Kürzeln
        ->orderBy(['land_de' => SORT_ASC])   // Optional: Alphabetische Sortierung
        ->all();
        
        $stadien = Stadiums::getStadiums();
        
        // View rendern
        return $this->render('view', [
            'club' => $club,
            'nation' => $nation,
            'stadium' => $stadium,
            'recentMatches' => $recentMatches,
            'upcomingMatches' => $upcomingMatches,
            'squad' => $squad,
            'nationalSquad' => $nationalSquad, // Übergebe nationalSquad an die View
            'isEditing' => $isEditing,
            'nationen' => $nationen,
            'stadien' => $stadien,
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
}
?>