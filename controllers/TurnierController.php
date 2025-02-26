<?php
namespace app\controllers;

use yii\web\Controller;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Turnier;
use app\models\Wettbewerb;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird
use yii\web\Response;

class TurnierController extends Controller
{
    public function actionView($wettbewerbID, $jahr, $gruppe = null, $runde = null, $spieltag = null)
    {
        // Daten aus der Tabelle "turnier" holen
        $spiele = Turnier::findTurniere($wettbewerbID, $jahr, $gruppe, $runde, $spieltag);
        $turniername = Helper::getTurniername($wettbewerbID); // Wettbewerbsname holen
        
        $turnierjahr = $jahr . '-01-01';
                
        // Teilnehmer abrufen
        $clubs = Turnier::findTeilnehmer($wettbewerbID, $jahr);
        
        // Anzahl der Tore sowie Platzverweise für die Turnierübersicht        
        $anzahlTore = Turnier::countTore($wettbewerbID, $jahr);
        $anzahlPlatzverweise = Turnier::countPlatzverweise($wettbewerbID, $jahr);
        
        // Spieleranzahl ergänzen
        foreach ($clubs as &$club) {
            $club['spieleranzahl'] = Turnier::countSpieler($wettbewerbID, $jahr, $club['id']) ?: '-----';
        }
        unset($club);
        
        $model = Turnier::findOne(['wettbewerbID' => $wettbewerbID, 'jahr' => $jahr]);
        $topScorers = $model->getTopScorers($wettbewerbID, $jahr);
        
        return $this->render('view', [
            'spiele' => $spiele,
            'turniername' => $turniername,
            'jahr' => $jahr,
            'wettbewerbID' => $wettbewerbID,
            'clubs' => $clubs, // Teilnehmer und Spieleranzahl an das View übergeben
            'anzahlTore' => $anzahlTore,
            'anzahlPlatzverweise' => $anzahlPlatzverweise,
            'topScorers' => $topScorers,
            'turnierjahr' => $turnierjahr,
            
        ]);
    }
    
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $term = Yii::$app->request->get('term'); // Das Suchfeld "term" wird von jQuery UI Autocomplete verwendet
        $clubs = Wettbewerb::find()
        ->select(['id', 'name as value']) // 'value' ist erforderlich für jQuery UI
        ->where(['like', 'name', $term])
        ->asArray()
        ->all();
        
        return $clubs;
    }
}
