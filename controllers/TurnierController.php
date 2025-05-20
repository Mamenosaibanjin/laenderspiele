<?php
namespace app\controllers;

use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii; // Für den Zugriff auf Yii::$app->request
use app\models\Referee;
use app\models\Spiel;
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
    
    public function actionSpielplan($tournamentID, $landID = null)
    {
        $turnier = Tournament::findOne($tournamentID);
        
        if (!$turnier) {
            throw new NotFoundHttpException('Turnier nicht gefunden.');
        }
        
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
                'statistikRunden' => []
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
