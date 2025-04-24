<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Spiel;
use app\models\Tournament;
use app\models\Turnier;
use app\models\Runde;
use app\models\Wettbewerb;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird
use yii\web\Response;

class TurnierController extends Controller
{
    public function actionTeilnehmer($wettbewerbID, $jahr, $gruppe = null, $runde = null, $spieltag = null)
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
    
    public function actionErgebnisse($wettbewerbID, $jahr)
    {
        $turnier = Tournament::find()->where(['wettbewerbID' => $wettbewerbID, 'jahr' => $jahr])->one();
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        // Alle Runden des Turniers (z.B. Gruppenphasen, KO-Runden)
        $runden = Runde::find()
        ->where(['turnierID' => $turnier->id])
        ->orderBy(['typ' => SORT_ASC, 'gruppenname' => SORT_ASC, 'name' => SORT_ASC])
        ->all();
        
        // Auswahl (optional: später via GET-Parameter steuerbar)
        $runde = $runden[0] ?? null;
        $spiele = [];
        
        if ($runde) {
            $spiele = Spiel::find()
            ->where(['rundeID' => $runde->id])
            ->orderBy(['datum' => SORT_ASC, 'zeit' => SORT_ASC])
            ->all();
        }
        
        return $this->render('ergebnisse', [
            'turnier' => $turnier,
            'runden' => $runden,
            'runde' => $runde,
            'spiele' => $spiele,
        ]);
    }
    
}
