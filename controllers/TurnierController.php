<?php
namespace app\controllers;

use yii\web\Controller;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Turnier;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird

class TurnierController extends Controller
{
    public function actionView($wettbewerbID, $jahr, $gruppe = null, $runde = null, $spieltag = null)
    {
        // Daten aus der Tabelle "turnier" holen
        $spiele = Turnier::findTurniere($wettbewerbID, $jahr, $gruppe, $runde, $spieltag);
        $turniername = Helper::getTurniername($wettbewerbID); // Wettbewerbsname holen
        
        // Teilnehmer abrufen
        $clubs = Turnier::findTeilnehmer($wettbewerbID, $jahr);
        
        // Spieleranzahl ergänzen
        foreach ($clubs as &$club) {
            $club['spieleranzahl'] = Turnier::countSpieler($wettbewerbID, $jahr, $club['id']) ?: '-----';
        }
        unset($club);
        
        return $this->render('view', [
            'spiele' => $spiele,
            'turniername' => $turniername,
            'jahr' => $jahr,
            'wettbewerbID' => $wettbewerbID,
            'clubs' => $clubs, // Teilnehmer und Spieleranzahl an das View übergeben
        ]);
    }
}
