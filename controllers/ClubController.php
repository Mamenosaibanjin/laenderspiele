<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Club;

class ClubController extends Controller
{
    public function actionView($id)
    {
        // Club-Datensatz laden
        $club = Club::findOne($id);
        if (!$club) {
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
        
        // View rendern
        return $this->render('view', [
            'club' => $club,
            'nation' => $nation,
            'stadium' => $stadium,
            'recentMatches' => $recentMatches,
            'upcomingMatches' => $upcomingMatches,
            'squad' => $squad,
            'nationalSquad' => $nationalSquad, // Übergebe nationalSquad an die View
        ]);
    }
}
?>