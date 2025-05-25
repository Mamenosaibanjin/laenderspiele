<?php
namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Referee;
use app\models\Spiel;
use app\models\Club;
use app\models\Spieler;
use app\models\Stadion;
use app\models\Tournament;
use app\models\Turnier;
use app\models\Runde;
use app\models\Wettbewerb;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird
use yii\web\Response;
use app\models\SpielerLandWettbewerb;
use yii\db\Expression;
use yii\db\Query;


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
        
        $term = Yii::$app->request->get('term');
        
        $tournaments = Tournament::find()
        ->alias('t')
        ->select([
            't.id',
            "CONCAT(w.name, ' ', t.jahr, ' (', t.land, ')') AS value"
        ])
        ->joinWith('wettbewerb w')
        ->where(['like', 'w.name', $term])
        ->limit(20)
        ->asArray()
        ->all();
        
        return $tournaments;
    }
    
    public function actionAnlegen()
    {
        $request = Yii::$app->request;
        
        if ($request->isPost) {
            $club1ID = $request->post('club1ID');
            $club2ID = $request->post('club2ID');
            $rundeID = $request->post('rundeID');
            $tournamentID = $request->post('tournamentID');
            $datum = $request->post('datum');
            $zeit = $request->post('zeit');
            
            // 1. Neues Spiel anlegen (in Tabelle "spiele")
            $spiel = new Spiel();
            $spiel->club1ID = $club1ID;
            $spiel->club2ID = $club2ID;
            
            if ($spiel->save()) {
                // 2. Details in turnier-Spiel-Tabelle speichern (angenommen: TurnierSpiel-Model)
                $turnierSpiel = new Turnier();
                $turnierSpiel->spielID = $spiel->id;
                $turnierSpiel->rundeID = $rundeID;
                $turnierSpiel->tournamentID = $tournamentID;
                $turnierSpiel->datum = $datum;
                $turnierSpiel->zeit = $zeit;
                
                if ($turnierSpiel->save()) {
                    // 3. Redirect auf passenden Spielplan
                    return $this->redirect(['/turnier/' . $tournamentID . '/spielplan']);
                } else {
                    Yii::$app->session->setFlash('error', 'Fehler beim Speichern der Spieldetails.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Fehler beim Anlegen des Spiels.');
            }
        }
        
        // Falls kein POST oder Fehler: ggf. Formular erneut anzeigen oder Fehlerseite
        return $this->render('anlegen', [
            // Daten für Formular ggf. hier übergeben
        ]);
    }
    
    
    public function actionErgebnisse($tournamentID, $rundeID = null)
    {
        // Wenn keine Parameter vorhanden sind → redirect auf die vollständige "Default"-URL
        if ($rundeID === null) {
            $runde = Turnier::find()
            ->alias('t')
            ->joinWith('runde r')
            ->where(['t.tournamentID' => $tournamentID])
            ->orderBy([
                'r.typ' => SORT_DESC,
                'r.sortierung' => SORT_DESC,
            ])
            ->limit(1)
            ->one();
            
            return $this->redirect([
                'turnier/ergebnisse',
                'tournamentID' => $tournamentID,
                'rundeID' => $runde->runde->id
            ]);
        }
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
    
    public function actionSpielplan($tournamentID, $landID = null)
    {
        $turnier = Tournament::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $vereine = \app\models\Club::find()
        ->select(['id', 'name', 'land']) 
        ->andWhere(['typID' => [1, 2, 3, 5]])
        ->orderBy('name')
        ->asArray()
        ->all();
        
        // Spiele ermitteln
        $spiele = [];
        $query = Turnier::find()
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
        ]);
        if (!empty($landID)) {
            $query->andWhere([
                'or',
                ['c1.id' => $landID],
                ['c2.id' => $landID],
            ]);
        }
        
        $spiele = $query->all();
        
        
        $vereinsDaten = array_map(function($v) {
            return [
                'label' => $v['name'],
                'value' => $v['id']
            ];
        }, $vereine);
        
        return $this->render('spielplan', [
            'turnier' => $turnier,
            'spiele' => $spiele,
            'vereinsDaten' => json_encode($vereinsDaten)
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
    
    public function actionStadien($tournamentID, $sort = null, $page = null)
    {
        // Wenn keine Parameter vorhanden sind → redirect auf die vollständige "Default"-URL
        if ($sort === null || $page === null) {
            return $this->redirect([
                'turnier/stadien',
                'tournamentID' => $tournamentID,
                'sort' => 'nach-kapazitaet',
                'page' => 1
            ]);
        }
        
        $turnier = Turnier::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $query = Stadion::find()
        ->alias('st')
        ->select([
            'st.id',
            'st.name',
            'st.stadt',
            'st.land',
            'st.kapazitaet'
        ])
        ->innerJoin('spiele s', 's.stadiumID = st.id')
        ->innerJoin('turnier t', 't.spielID = s.id')
        ->where(['t.tournamentID' => $tournamentID])
        ->groupBy('st.id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
            'sort' => [
                'attributes' => [
                    'stadiumID',
                    'nach-name' => [
                        'asc' => ['st.name' => SORT_ASC],
                        'desc' => ['st.name' => SORT_DESC],
                        'label' => 'Name'
                    ],
                    'nach-stadt' => [
                        'asc' => ['st.stadt' => SORT_ASC],
                        'desc' => ['st.stadt' => SORT_DESC],
                        'label' => 'Stadt'
                    ],
                    'nach-land' => [
                        'asc' => ['st.land' => SORT_ASC],
                        'desc' => ['st.land' => SORT_DESC],
                        'label' => 'Land'
                    ],
                    'nach-kapazitaet' => [
                        'asc' => ['st.kapazitaet' => SORT_ASC],
                        'desc' => ['st.kapazitaet' => SORT_DESC],
                        'label' => 'Kapazität'
                    ]
                ],
                'defaultOrder' => $sort === 'nach-kapazitaet' ? ['spielerName' => SORT_ASC] : [],
            ],
        ]);
        
        return $this->render('stadien', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'jahr' => Helper::getTurnierJahr($tournamentID),
            'wettbewerbID' => Helper::getWettbewerbID($tournamentID),
            'turnierjahr' => Helper::getTurnierStartdatum($tournamentID),
            'dataProvider' => $dataProvider
        ]);
    }
    
    public function actionToreProSaison($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $statistikTore = [];
        
        foreach ($alleTurniere as $turnierItem) {
            $tid = $turnierItem['id'];
            
            // Anzahl Spiele
            $spiele = (new \yii\db\Query())
            ->from('turnier')
            ->where(['tournamentID' => $tid])
            ->count();
            
            // Anzahl Tore
            $tore = (new \yii\db\Query())
            ->from('games g')
            ->innerJoin('turnier t', 't.spielID = g.spielID')
            ->where(['t.tournamentID' => $tid])
            ->andWhere(['or',
                ['g.aktion' => 'TOR'],
                ['g.aktion' => 'ET'],
                ['and', ['g.aktion' => '11m'], ['<', 'g.minute', 200]]
            ])
            ->count();
            
            // Berechnung Durchschnitt
            $durchschnitt = $spiele > 0 ? round($tore / $spiele, 4) : 0;
            
            $statistikTore[] = [
                'tournamentID' => $tid,
                'spiele' => (int) $spiele,
                'tore' => (int) $tore,
                'durchschnitt' => $durchschnitt,
            ];
        }
        
        // Sortiere nach Durchschnitt absteigend
        usort($statistikTore, fn($a, $b) => $b['durchschnitt'] <=> $a['durchschnitt']);
        
        return $this->render('toreProSaison', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'statistikTore' => $statistikTore
        ]);
    }
    
    public function actionToreProRunde($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $tournamentIDs = array_column($alleTurniere, 'id');
        
        // Falls keine Turniere gefunden wurden
        if (empty($tournamentIDs)) {
            return $this->render('toreProRunde', [
                'tournamentID' => $tournamentID,
                'turniername' => Helper::getTurniername($tournamentID),
                'statistikTore' => []
            ]);
        }
        
        // Hole Statistiken aus der DB (Games + Tore pro Runde)
        $subQuery = (new Query())
        ->select([
            't.tournamentID',
            't.rundeID',
            new Expression("
            COUNT(CASE
                WHEN g.aktion IN ('TOR', 'ET') OR (g.aktion = '11m' AND g.minute < 200)
                THEN 1 ELSE NULL END
            ) AS tore
        "),
            'COUNT(DISTINCT t.spielID) AS spiele',
            new Expression("
            ROUND(
                COUNT(CASE
                    WHEN g.aktion IN ('TOR', 'ET') OR (g.aktion = '11m' AND g.minute < 200)
                    THEN 1 ELSE NULL END
                ) * 1.0 / COUNT(DISTINCT t.spielID), 4
            ) AS durchschnitt
        ")
        ])
        ->from(['t' => 'turnier'])
        ->leftJoin(['g' => 'games'], 'g.spielID = t.spielID')
        ->where(['t.tournamentID' => $tournamentIDs]) // array of IDs
        ->groupBy(['t.tournamentID', 't.rundeID'])
        ->having(['>', 'spiele', 0])
        ->orderBy(['durchschnitt' => SORT_DESC])
        ->limit(50);
        
        $statistikRunden = $subQuery->all();
        
        return $this->render('toreProRunde', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'statistikTore' => $statistikRunden
        ]);
    }
    
    public function actionHoechsteSiege($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $tournamentIDs = array_column($alleTurniere, 'id');
        
        // Falls keine Turniere gefunden wurden
        if (empty($tournamentIDs)) {
            return $this->render('toreProRunde', [
                'tournamentID' => $tournamentID,
                'turniername' => Helper::getTurniername($tournamentID),
                'hoechsteSiege' => []
            ]);
        }
        
        // Hole die höchsten Siege (nach Tordifferenz)
        $hoechsteSiege = (new Query())
        ->select([
            't.tournamentID',
            't.rundeID',
            't.datum',
            's.id',
            's.club1ID',
            's.tore1',
            's.tore2',
            's.extratime',
            's.penalty',
            's.club2ID',
            // Tordifferenz
            'differenz' => new Expression('ABS(s.tore1 - s.tore2)'),
            // Gesamttore zur sekundären Sortierung
            'gesamtTore' => new Expression('s.tore1 + s.tore2'),
        ])
        ->from(['t' => 'turnier'])
        ->innerJoin(['s' => 'spiele'], 's.id = t.spielID')
        ->where(['t.tournamentID' => $tournamentIDs])
        ->andWhere(['<>', 's.tore1', 's.tore2']) // Nur Siege
        ->orderBy([
            'differenz' => SORT_DESC,
            'gesamtTore' => SORT_DESC
        ])
        ->limit(50)
        ->all();
        
        return $this->render('hoechsteSiege', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'hoechsteSiege' => $hoechsteSiege
        ]);
    }
    
    public function actionTorreichsteSpiele($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $tournamentIDs = array_column($alleTurniere, 'id');
        
        // Falls keine Turniere gefunden wurden
        if (empty($tournamentIDs)) {
            return $this->render('toreProRunde', [
                'tournamentID' => $tournamentID,
                'turniername' => Helper::getTurniername($tournamentID),
                'hoechsteSiege' => []
            ]);
        }
        
        // Hole die torreichsten Spiele
        $torreichsteSpiele = (new Query())
        ->select([
            't.tournamentID',
            't.rundeID',
            't.datum',
            's.id',
            's.club1ID',
            's.tore1',
            's.tore2',
            's.extratime',
            's.penalty',
            's.club2ID',
            // Zähle echte Tore aus games-Tabelle
            'gesamtTore' => new Expression("
            COUNT(CASE
                WHEN g.aktion IN ('TOR', 'ET') OR (g.aktion = '11m' AND g.minute < 200)
                THEN 1 ELSE NULL
            END)
        ")
        ])
        ->from(['t' => 'turnier'])
        ->innerJoin(['s' => 'spiele'], 's.id = t.spielID')
        ->leftJoin(['g' => 'games'], 'g.spielID = s.id')
        ->where(['t.tournamentID' => $tournamentIDs])
        ->groupBy([
            't.tournamentID',
            't.rundeID',
            't.datum',
            's.id',
            's.club1ID',
            's.tore1',
            's.tore2',
            's.extratime',
            's.penalty',
            's.club2ID',
        ])
        ->having(['>', 'gesamtTore', 0]) // Nur Spiele mit mindestens 1 regulärem Tor
        ->orderBy(['gesamtTore' => SORT_DESC])
        ->limit(50)
        ->all();
        
        
        return $this->render('torreichsteSpiele', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'torreichsteSpiele' => $torreichsteSpiele
        ]);
    }
    
    public function actionMeisteToreEinesSpielers($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $tournamentIDs = array_column($alleTurniere, 'id');
        
        // Falls keine Turniere gefunden wurden
        if (empty($tournamentIDs)) {
            return $this->render('meisteToreEinesSpielers', [
                'tournamentID' => $tournamentID,
                'turniername' => Helper::getTurniername($tournamentID),
                'hoechsteSiege' => []
            ]);
        }
        
        // Hole die meisten Tore eines Spielers
        $meisteTore = (new \yii\db\Query())
        ->select([
            'g.spielerID',
            't.datum',
            't.spielID',
            's.penalty',
            's.extratime',
            's.tore1',
            's.tore2',
            'anzahlTore' => new Expression("
            COUNT(
                CASE
                    WHEN g.aktion IN ('TOR', '11m') AND g.minute < 200
                    THEN 1 ELSE NULL
                END
            )
        "),
        ])
        ->from(['g' => 'games'])
        ->innerJoin(['t' => 'turnier'], 't.spielID = g.spielID')
        ->innerJoin(['s' => 'spiele'], 's.id = t.spielID')
        ->where(['t.tournamentID' => $tournamentIDs])
        ->andWhere([
            'or',
            ['g.aktion' => 'TOR'],
            ['and', ['g.aktion' => '11m'], ['<', 'g.minute', 200]]
        ])
        ->groupBy(['g.spielerID', 'g.spielID', 't.datum', 't.spielID'])
        ->having(['>=', 'anzahlTore', 2])
        ->orderBy(['anzahlTore' => SORT_DESC])
        ->all();
        
        return $this->render('meisteToreEinesSpielers', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'meisteTore' => $meisteTore
        ]);
    }
    
    public function actionUnfairsteSpiele($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $alleTurniere = Turnier::findAlleTurniere($tournamentID, true);
        $tournamentIDs = array_column($alleTurniere, 'id');
        
        // Falls keine Turniere gefunden wurden
        if (empty($tournamentIDs)) {
            return $this->render('toreProRunde', [
                'tournamentID' => $tournamentID,
                'turniername' => Helper::getTurniername($tournamentID),
                'unfairsteSpiele' => []
            ]);
        }
        
        // Hole die unfairsten Spiele
        $unfairsteSpiele = (new Query())
        ->select([
            't.tournamentID',
            't.rundeID',
            't.datum',
            's.id',
            's.club1ID',
            's.club2ID',
            's.tore1',
            's.tore2',
            // Karten-Zählungen
            'gk' => new Expression("COUNT(CASE WHEN g.aktion = 'GK' THEN 1 ELSE NULL END)"),
            'grk' => new Expression("COUNT(CASE WHEN g.aktion = 'GRK' THEN 1 ELSE NULL END)"),
            'rk' => new Expression("COUNT(CASE WHEN g.aktion = 'RK' THEN 1 ELSE NULL END)"),
            // Gewichtete Punkteberechnung
            'punkte' => new Expression("
            SUM(
                CASE
                    WHEN g.aktion = 'GK' THEN 1
                    WHEN g.aktion = 'GRK' THEN 2
                    WHEN g.aktion = 'RK' THEN 3
                    ELSE 0
                END
            )
        ")
        ])
        ->from(['t' => 'turnier'])
        ->innerJoin(['s' => 'spiele'], 's.id = t.spielID')
        ->leftJoin(['g' => 'games'], 'g.spielID = s.id')
        ->where(['t.tournamentID' => $tournamentIDs])
        ->groupBy([
            't.tournamentID',
            't.rundeID',
            't.datum',
            's.id',
            's.club1ID',
            's.club2ID',
            's.tore1',
            's.tore2',
        ])
        ->having(['>', 'punkte', 0]) // Nur Spiele mit mindestens einer Karte
        ->orderBy([
            'punkte' => SORT_DESC,
            'rk' => SORT_DESC,
            'grk' => SORT_DESC,
            'gk' => SORT_DESC,
        ])
        ->limit(50)
        ->all();
        
        return $this->render('unfairsteSpiele', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'unfairsteSpiele' => $unfairsteSpiele
        ]);
    }
    
    public function actionSpieleImStadion($stadionID, $tournamentID)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        
        return \app\components\StadiumHelper::getGamesAtTournament($stadionID, $tournamentID);
    }
    
    public function actionSchiedsrichter($tournamentID, $sort = null, $page = null)
    {
        // Wenn keine Parameter vorhanden sind → redirect auf die vollständige "Default"-URL
        if ($sort === null || $page === null) {
            return $this->redirect([
                'turnier/schiedsrichter',
                'tournamentID' => $tournamentID,
                'sort' => 'nach-name',
                'page' => 1
            ]);
        }
        
        $referee = Turnier::findOne($tournamentID);
        
        if (!$referee) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        $query = Referee::find()
        ->alias('r')
        ->select([
            'r.id',
            'r.name',
            'r.vorname',
            'r.geburtstag',
            'r.nati1',
            new Expression('COUNT(DISTINCT s.id) AS spiele'),
            new Expression("COUNT(DISTINCT g_gk.id) AS gk_count"),
            new Expression("COUNT(DISTINCT g_grk.id) AS grk_count"),
            new Expression("COUNT(DISTINCT g_rk.id) AS rk_count"),
        ])
        ->innerJoin(['s' => 'spiele'], 's.referee1ID = r.id')
        ->innerJoin(['t' => 'turnier'], 't.spielID = s.id')
        ->leftJoin(['g_gk' => 'games'], "g_gk.spielID = s.id AND g_gk.aktion LIKE 'GK'")
        ->leftJoin(['g_grk' => 'games'], "g_grk.spielID = s.id AND g_grk.aktion LIKE 'GRK'")
        ->leftJoin(['g_rk' => 'games'], "g_rk.spielID = s.id AND g_rk.aktion LIKE 'RK'")
        ->where(['t.tournamentID' => $tournamentID])
        ->groupBy('r.id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 25],
            'sort' => [
                'attributes' => [
                    'refereeID',
                    'nach-name' => [
                        'asc' => ['r.name' => SORT_ASC],
                        'desc' => ['r.name' => SORT_DESC],
                        'label' => 'Name'
                    ],
                    'nach-geburtstag' => [
                        'asc' => ['r.geburtstag' => SORT_ASC],
                        'desc' => ['r.geburtstag' => SORT_DESC],
                        'label' => 'geboren'
                    ],
                    'nach-land' => [
                        'asc' => ['r.nati1' => SORT_ASC],
                        'desc' => ['r.nati1' => SORT_DESC],
                        'label' => 'Land'
                    ],
                    'nach-spiele' => [
                        'asc' => ['spiele' => SORT_ASC],
                        'desc' => ['spiele' => SORT_DESC],
                        'label' => 'Spiele'
                    ],
                    'nach-gelbe-karten' => [
                        'asc' => ['gk_count' => SORT_ASC],
                        'desc' => ['gk_count' => SORT_DESC],
                        'label' => 'GK'
                    ],
                    'nach-gelbrote-karten' => [
                        'asc' => ['grk_count' => SORT_ASC],
                        'desc' => ['grk_count' => SORT_DESC],
                        'label' => 'GRK'
                    ],
                    'nach-rote-karten' => [
                        'asc' => ['rk_count' => SORT_ASC],
                        'desc' => ['rk_count' => SORT_DESC],
                        'label' => 'RK'
                    ],
                ],
                'defaultOrder' => ['spiele' => SORT_DESC, 'r.name' => SORT_ASC],
            ],
        ]);
        
        return $this->render('schiedsrichter', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'jahr' => Helper::getTurnierJahr($tournamentID),
            'wettbewerbID' => Helper::getWettbewerbID($tournamentID),
            'turnierjahr' => Helper::getTurnierStartdatum($tournamentID),
            'dataProvider' => $dataProvider
        ]);
    }
    
    public function actionSchiedsrichterSpiele($refereeID, $tournamentID)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        
        return \app\components\RefereeHelper::getGamesAtTournament($refereeID, $tournamentID);
    }
    
    public function actionArchiv($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        // Teilnehmer abrufen
        $turniere = Turnier::findAlleTurniere($tournamentID);
        
        return $this->render('archiv', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'turniere' => $turniere
        ]);
    }
    
    public function actionAlleSieger($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        // Teilnehmer abrufen
        $turniere = Turnier::findAlleTurniere($tournamentID, true);
        
        return $this->render('alleSieger', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'turniere' => $turniere
        ]);
    }
    
    public function actionAlleTorjaeger($tournamentID)
    {
        $turnier = Turnier::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
        // Teilnehmer abrufen
        $turniere = Turnier::findAlleTurniere($tournamentID, true);
        
        return $this->render('alleTorjaeger', [
            'tournamentID' => $tournamentID,
            'turniername' => Helper::getTurniername($tournamentID),
            'turniere' => $turniere
        ]);
    }
    
}
