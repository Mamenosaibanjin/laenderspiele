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
    public function actionTeilnehmer($tournamentID, $gruppe = null, $runde = null, $spieltag = null)
    {
        // Daten aus der Tabelle "turnier" holen
        $spiele = Turnier::findTurniere($tournamentID, $gruppe, $runde, $spieltag);
        $turniername = Helper::getTurniername($tournamentID); // Wettbewerbsname holen
               
        // Teilnehmer abrufen
        $clubs = Turnier::findTeilnehmer($tournamentID);
        
        // Anzahl der Tore sowie Platzverweise für die Turnierübersicht        
        $anzahlTore = Turnier::countTore($tournamentID);
        $anzahlPlatzverweise = Turnier::countPlatzverweise($tournamentID);
        
        // Spieleranzahl ergänzen
        foreach ($clubs as &$club) {
            $club['spieleranzahl'] = Turnier::countSpieler($tournamentID, $club['id']) ?: '-----';
        }
        unset($club);
        
        $model = Turnier::findOne($tournamentID);
        $topScorers = $model->getTopScorers($tournamentID);
        
        $jahr = Helper::getTurnierJahr($tournamentID);
        $wettbewerbID = Helper::getWettbewerbID($tournamentID);
        $turnierjahr = Helper::getTurnierStartdatum($tournamentID);
        
        return $this->render('view', [
            'spiele' => $spiele,
            'jahr' => $jahr, // 
            'wettbewerbID' => $wettbewerbID, // 
            'turniername' => $turniername,
            'tournamentID' => $tournamentID,
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
    
    public function actionErgebnisse($tournamentID)
    {
        $turnier = Tournament::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        
        $runden = Turnier::find()
        ->alias('t')
        ->joinWith('runde r')  // korrekt, denn Turnier hat 'runde'
        ->where(['t.tournamentID' => $tournamentID])
        ->orderBy([
            'r.typ' => SORT_ASC,
            'r.name' => SORT_ASC,
            'r.sortierung' => SORT_ASC
        ])
        ->select([
            'r.id AS id',
            'r.sortierung',
            'r.typ',
            'r.name AS name',
            't.tournamentID AS tournamentID'
        ])
        ->distinct()
        ->all();
        
        // 1. Runden für das Dropdown vorbereiten
        $runde = Runde::find()
        ->alias('r')
        ->innerJoin('turnier t', 't.rundeID = r.id')
        ->where(['t.tournamentID' => $tournamentID])
        ->orderBy([
            'r.typ' => SORT_ASC,
            'r.sortierung' => SORT_ASC,
        ])
        ->limit(1)
        ->one();
        
        // 2. Runde wählen: entweder per GET oder automatisch
        $rundeID = Yii::$app->request->get('rundeID');
        if ($rundeID) {
            $runde = Runde::findOne($rundeID);
        } else {
            // erste passende Runde automatisch wählen
            $runde = Turnier::find()
            ->alias('t')
            ->joinWith('runde r')
            ->where(['t.tournamentID' => $tournamentID])
            ->orderBy([
                'r.typ' => SORT_ASC,
                'r.sortierung' => SORT_ASC,
            ])
            ->limit(1)
            ->one();
        }
        
        // 3. Spiele ermitteln
        $spiele = [];
        if ($runde) {
            $spiele = Turnier::find()
            ->alias('t')
            ->joinWith([
                'runde r',
                'spiel s',
                'spiel.club1 c1',
                'spiel.club2 c2',
            ])
            ->where([
                't.tournamentID' => $tournamentID,
                't.rundeID' => $rundeID
            ])
            ->orderBy([
                'r.typ' => SORT_ASC,
                'r.sortierung' => SORT_ASC,
                't.datum' => SORT_ASC,
                't.zeit' => SORT_ASC,
            ])
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
