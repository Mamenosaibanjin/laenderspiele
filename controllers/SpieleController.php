<?php
namespace app\controllers;

use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Spiel;
use app\models\Turnier;
use app\models\Wettbewerb;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird
use Yii;

class SpieleController extends Controller
{
        public function actionGetTeams()
        {
            if (Yii::$app->request->isAjax) {
                $wettbewerbID = Yii::$app->request->post('wettbewerbID');
                $jahr = Yii::$app->request->post('jahr');
                
                $teams = Turnier::findTeilnehmer($wettbewerbID, $jahr);
                $competitionName = Helper::getTurniername($wettbewerbID);
                
                return $this->renderAjax('_teams_partial', [
                    'teams' => $teams,
                    'competitionName' => $competitionName,
                    'jahr' => $jahr
                ]);
            }
            throw new BadRequestHttpException('Ungültige Anfrage');
        }
        
        public function actionGetCompetitions()
        {
            if (Yii::$app->request->isAjax) {
                $competitions = Wettbewerb::find()->all();
                return $this->renderAjax('_competitions_partial', ['competitions' => $competitions]);
            }
            throw new BadRequestHttpException('Ungültige Anfrage');
        }
        
        public function actionView($tournamentID = null, $wettbewerbID = null, $jahr = null, $gruppe = null, $runde = null, $spieltag = null)
        {
            $model = new Turnier();
            
            if ($tournamentID !== null) {
                // Neuer Weg: Direkt über tournamentID arbeiten
                $tournament = Turnier::findOne($tournamentID);
                if (!$tournament) {
                    throw new NotFoundHttpException('Turnier nicht gefunden.');
                }
                // Hole alle Spiele zu diesem Turnier (vielleicht später anpassen, jetzt dummyhaft)
                $spiele = Turnier::find()
                ->alias('t')
                ->joinWith('runde r') // jetzt kommt der JOIN
                ->where(['t.tournamentID' => $tournament->id])
                ->orderBy([
                    'r.typ' => SORT_ASC,
                    'r.sortierung' => SORT_ASC,
                    't.datum' => SORT_ASC,
                    't.zeit' => SORT_ASC,
                ])
                ->all();
                
                
                $jahr = Helper::getTurnierJahr($tournamentID);
                $wettbewerbID = Helper::getWettbewerbID($tournamentID);
                $turnierjahr = Helper::getTurnierStartdatum($tournamentID);
                
                $turniername = Helper::getTurniername($tournament->wettbewerbID);
                
            } elseif ($wettbewerbID !== null && $jahr !== null) {
                // Alter Weg: über wettbewerbID + jahr
                $spiele = Turnier::findTurniere($wettbewerbID, $jahr, $gruppe, $runde, $spieltag);
                $turniername = Helper::getTurniername($wettbewerbID);
                $turnierjahr = $jahr . '-01-01';
            } else {
                throw new BadRequestHttpException('Ungültige Parameter.');
            }
            
            return $this->render('view', [
                'spiele' => $spiele,
                'turniername' => $turniername,
                'tournamentID' => $tournamentID,
                'jahr' => $jahr,
                'model' => $model,
                'turnierjahr' => $turnierjahr,
            ]);
        }
        
        
        public function actionCreate()
        {
            $spiel = new Spiel();
            $turnier = new Turnier();
            
            if (Yii::$app->request->isPost) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Hole und validiere die POST-Daten
                    $spiel->club1ID = Yii::$app->request->post('club1ID');
                    $spiel->club2ID = Yii::$app->request->post('club2ID');
                    
                    if (!$spiel->save()) {
                        throw new \Exception('Fehler beim Speichern des Spiels: ' . json_encode($spiel->errors));
                    }
                    
                    // Hol die ID des gespeicherten Spiels
                    $spielID = $spiel->id;
                    $turnier->wettbewerbID = Yii::$app->request->post('wettbewerbID') ?? null;
                    $turnier->jahr = Yii::$app->request->post('Turnier')['jahr'] ?? null;
                    $turnier->datum = Yii::$app->request->post('Turnier')['datum'] ?? null;
                    $turnier->zeit = Yii::$app->request->post('Turnier')['zeit'] ?? null;
                    $turnier->gruppe = Yii::$app->request->post('Turnier')['gruppe'] ?? null;
                    $turnier->spieltag = Yii::$app->request->post('Turnier')['spieltag'] ?? 0;
                    $turnier->runde = Yii::$app->request->post('Turnier')['runde'] ?? 0;
                    $turnier->spielID = $spielID;
                    $turnier->aktiv = 0;
                    $turnier->tore = 0;
                    $turnier->beschriftung = Yii::$app->request->post('Turnier')['beschriftung'] ?? '';
                    
                    if (!$turnier->save()) {
                        throw new \Exception('Fehler beim Speichern des Turniers: ' . json_encode($turnier->errors));
                    }
                    
                    // Commit der Transaktion
                    $transaction->commit();
                    
                    // Weiterleitung zur Ansicht der gespeicherten Daten
                    return $this->redirect(['spiele/view', 'wettbewerbID' => $turnier->wettbewerbID, 'jahr' => $turnier->jahr]);
                    
                } catch (\Exception $e) {
                    // Rollback bei Fehlern
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
            
            // Falls kein POST-Request oder Fehler: Zurück zur Hauptseite
            return $this->redirect(['spiele/view', 'wettbewerbID' => Yii::$app->request->post('wettbewerbID'), 'jahr' => Yii::$app->request->post('Turnier')['jahr']]);
        }
        
        public function actionUpdateDatetime()
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            // JSON-Daten vom Client einlesen
            $rawData = Yii::$app->request->getRawBody();
            $data = json_decode($rawData, true); // JSON in ein Array konvertieren

            $spielId = $data['spielId'] ?? null;
            $datetime = $data['datetime'] ?? null;
            
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            //if (!$spielId || !$datetime) {
            if (!$spielId) {
                return ['success' => false, 'error' => 'Ungültiges Spiel'];
            } 
            if (!$datetime) {
                return ['success' => false, 'error' => 'Ungültiges Dartum'];
            }
               // return ['success' => false, 'error' => 'Ungültige Eingabe'];
            //}
            
            $turnier = Turnier::findOne(['spielID' => $spielId]);
            if (!$turnier) {
                return ['success' => false, 'error' => 'Turnier nicht gefunden'];
            }
            // Datum und Zeit trennen
            $dateTimeParts = explode('T', $datetime);
            if (count($dateTimeParts) !== 2) {
                return ['success' => false, 'error' => 'Ungültiges Datetime-Format'];
            }
            
            $turnier->datum = $dateTimeParts[0];
            $turnier->zeit = $dateTimeParts[1];
            
            if ($turnier->save()) {
                return ['success' => true];
            } else {
                // Zusätzliche Debug-Informationen für den Fehler
                return [
                    'success' => false,
                    'error' => 'Fehler beim Speichern des Turniers',
                    'details' => $turnier->getErrors(),
                    'attributes' => $turnier->attributes
                ];
            }
        }
        
        public function actionDelete()
        {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $rawData = Yii::$app->request->getRawBody();
            $data = json_decode($rawData, true); // JSON in ein Array konvertieren
            
            if (!empty($data['spielId'])) {
                $transaction = \Yii::$app->db->beginTransaction(); // Transaktion starten
                try {
                    // Datensatz aus 'spiele' löschen
                    $spiel = Spiel::findOne($data['spielId']);
                    if ($spiel === null) {
                        throw new \Exception('Spiel-Datensatz nicht gefunden.');
                    }
                    
                    // Datensatz aus 'turnier' löschen (angenommen, es gibt eine spielID-Spalte)
                    $turnier = Turnier::find()->where(['spielID' => $spiel->id])->one();
                    if ($turnier !== null && !$turnier->delete()) {
                        throw new \Exception('Turnier-Datensatz konnte nicht gelöscht werden.');
                    }
                    
                    // Spiel-Datensatz löschen
                    if (!$spiel->delete()) {
                        throw new \Exception('Spiel-Datensatz konnte nicht gelöscht werden.');
                    }
                    
                    $transaction->commit(); // Transaktion erfolgreich
                    return ['success' => true];
                } catch (\Exception $e) {
                    $transaction->rollBack(); // Transaktion rückgängig machen
                    return ['success' => false, 'error' => $e->getMessage()];
                }
            }
            
            return ['success' => false, 'error' => 'Ungültige Daten.'];
        }
        
}
?>