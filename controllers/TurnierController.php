<?php
namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Spiel;
use app\models\Spieler;
use app\models\Tournament;
use app\models\Turnier;
use app\models\Runde;
use app\models\Wettbewerb;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird
use yii\web\Response;
use app\models\SpielerLandWettbewerb;

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
    
    public function actionSpielplan($tournamentID)
    {
        $turnier = Tournament::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        // Spiele ermitteln
        $spiele = [];
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
        ])
        ->orderBy([
            'r.typ' => SORT_ASC,
            'r.sortierung' => SORT_ASC,
            't.datum' => SORT_ASC,
            't.zeit' => SORT_ASC,
        ])
        ->all();
        
        return $this->render('spielplan', [
            'turnier' => $turnier,
            'spiele' => $spiele,
        ]);
    }
    
    public function actionSpieler($tournamentID, $positionen = null, $sort = null, $page = null)
    {
        // Wenn keine Parameter vorhanden sind → redirect auf die vollständige "Default"-URL
        if ($positionen === null || $sort === null || $page === null) {
            $defaultPositionen = '1,2,3,4,5,6,7'; // oder '0' → dann im Code behandeln
            return $this->redirect([
                'turnier/spieler',
                'tournamentID' => $tournamentID,
                'positionen' => $defaultPositionen,
                'sort' => 'nach-name',
                'page' => 1
            ]);
        }
        // Positionen als Array auflösen (z. B. "1,3,5" → [1,3,5])
        $positionsArray = $positionen ? explode(',', $positionen) : [];
        
        $turnier = Tournament::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        //        $selectedPositionen = Yii::$app->request->get('positionen', []); // Checkbox-Auswahl
        
        $query = SpielerLandWettbewerb::find()
        ->alias('slw')
        ->where(['tournamentID' => $tournamentID])
        ->joinWith(['spieler s'])
        ->joinWith(['land c']);
        
        if (!empty($positionsArray)) {
            $query->andWhere(['slw.positionID' => $positionsArray]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25, 'page' => $page - 1],
            'sort' => [
                'attributes' => [
                    'spielerID',
                    'nach-name' => [
                        'asc' => ['s.name' => SORT_ASC],
                        'desc' => ['s.name' => SORT_DESC],
                        'label' => 'Name'
                    ],
                    'nach-geburtstag' => [
                        'asc' => ['s.geburtstag' => SORT_ASC],
                        'desc' => ['s.geburtstag' => SORT_DESC],
                        'label' => 'Geboren'
                    ],
                    'nach-mannschaft' => [
                        'asc' => ['c.name' => SORT_ASC],
                        'desc' => ['c.name' => SORT_DESC],
                        'label' => 'Mannschaft'
                    ],
                    'nach-groesse' => [
                        'asc' => ['s.height' => SORT_ASC],
                        'desc' => ['s.height' => SORT_DESC],
                        'label' => 'Größe'
                    ],
                    'nach-position' => [
                        'asc' => ['positionID' => SORT_ASC],
                        'desc' => ['positionID' => SORT_DESC],
                        'label' => 'Position'
                    ]
                ],
                'defaultOrder' => $sort === 'nach-name' ? ['spielerName' => SORT_ASC] : [],
            ],
        ]);
        
        // Für Checkboxen: Alle Positionen laden
        $allePositionen = \app\models\Position::find()
        ->where(['between', 'id', 1, 7])
        ->orderBy(['id' => SORT_ASC])
        ->all();
        
        return $this->render('spieler', [
            'dataProvider' => $dataProvider,
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'jahr' => Helper::getTurnierJahr($tournamentID),
            'allePositionen' => $allePositionen,
            'selectedPositionen' => $positionsArray,
        ]);
    }
    
    public function actionTorjaeger($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        //        $selectedPositionen = Yii::$app->request->get('positionen', []); // Checkbox-Auswahl
        
        $topScorers = Spieler::find()
        ->select([
            'spieler.nati1',
            'spieler.id',
            'spieler.vorname',
            'spieler.name',
            'COUNT(CASE WHEN games.aktion LIKE "TOR" OR games.aktion LIKE "11m" THEN 1 END) AS tor',
            'COUNT(CASE WHEN games.aktion LIKE "11m" THEN 1 END) AS 11m'
        ])
        ->joinWith(['games', 'games.spiel.turnier'])
        ->where([
            'turnier.tournamentID' => $tournamentID
        ])
        ->andWhere(['or', ['=', 'games.aktion', 'TOR'], ['=', 'games.aktion', '11m']])
        ->andWhere(['<', 'games.minute', 199])
        ->groupBy('spieler.id')
        ->orderBy(['tor' => SORT_DESC, 'spieler.name' => SORT_ASC])
        ->asArray()
        ->all();
        
        return $this->render('torjaeger', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'jahr' => Helper::getTurnierJahr($tournamentID),
            'wettbewerbID' => Helper::getWettbewerbID($tournamentID),
            'turnierjahr' => Helper::getTurnierStartdatum($tournamentID),
            'topScorers' => $topScorers
        ]);
    }
    
}
