<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Games;
use app\models\Spiel;
use app\models\Spieler;
use app\models\SpielerVereinSaison;
use app\models\SpielerLandWettbewerb;
use Yii;

class SpielberichtController extends Controller
{
    public function actionView($id) {
        $spiel = Spiel::findOne($id);
        
        if (!$spiel) {
            throw new NotFoundHttpException('Spiel nicht gefunden.');
        }
        
        $aufstellung1 = $spiel->aufstellung1;
        $aufstellung2 = $spiel->aufstellung2;
        
        $highlightAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['TOR', '11m', 'ET', 'RK', 'GRK']])
        ->andWhere(['<', 'minute', 200]) // Bedingung für Minuten kleiner als 200
        ->orderBy(['minute' => SORT_ASC])
        ->all();
                
        $toreAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['TOR', '11m', 'ET', '11mX']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $kartenAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['GK', 'RK', 'GRK']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselHeim = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->andWhere(['zusatz' => ['H']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselAuswaerts = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->andWhere(['zusatz' => ['A']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $besondereAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['11mX']])
        ->andWhere(['<', 'minute', 200]) // Bedingung für Minuten kleiner als 200
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
       
        return $this->render('view', [
            'spiel' => $spiel,
            'aufstellung1' => $aufstellung1,
            'aufstellung2' => $aufstellung2,
            'highlightAktionen' => $highlightAktionen,
            'toreAktionen' => $toreAktionen,
            'kartenAktionen' => $kartenAktionen,
            'wechselAktionen' => $wechselAktionen,
            'besondereAktionen' => $besondereAktionen,
            'wechselHeim' => $wechselHeim,
            'wechselAuswaerts' => $wechselAuswaerts,
        ]);
    }
    
    public function actionUpdateLineup()
    {
        $request = Yii::$app->request;
        $rawBody = $request->getRawBody();
        
        if ($request->isPost) {
            $data = json_decode($request->getRawBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                
                return $this->asJson(['success' => false, 'message' => 'Ungültiges JSON']);
            }
            
            $spielID = $data['spielID'] ?? null;
            $type = $data['type'] ?? null;
            
            if (!$spielID || !$type) {
                return $this->asJson(['success' => false, 'message' => 'Fehlende Parameter']);
            }
            
            // Aufstellungs-ID anhand von spielID und type ermitteln
            $spiel = Spiel::findOne($spielID);
            if (!$spiel) {
                return $this->asJson(['success' => false, 'message' => 'Ungültige Spiel-ID']);
            }
            
            $aufstellungsID = $type === 'H' ? $spiel->aufstellung1ID : $spiel->aufstellung2ID;
            
            if (!$aufstellungsID) {
                return $this->asJson(['success' => false, 'message' => 'Ungültige Aufstellungs-ID']);
            }
            
            // Spieler und Coach aktualisieren
            try {
                foreach ($data['spieler'] as $spieler) {
                    // Spieler-ID extrahieren
                    $column = key($spieler); // Schlüsselname, z. B. "spieler1ID"
                    $value = reset($spieler); // Erster Wert, z. B. "48982"
                    
                    $command = Yii::$app->db->createCommand()
                    ->update(
                        'aufstellung',
                        [$column => $value],
                        ['ID' => $aufstellungsID]
                        );
                    
                    $executedSql[] = $command->rawSql; // SQL sammeln
                    $command->execute();
                }
                
                $coachCommand = Yii::$app->db->createCommand()
                ->update(
                    'aufstellung',
                    ['coachID' => $data['coachID']],
                    ['ID' => $aufstellungsID]
                    );
                
                $executedSql[] = $coachCommand->rawSql; // SQL sammeln
                $coachCommand->execute();
                
                return $this->asJson([
                    'success' => true,
                    'executedSql' => $executedSql, // Alle SQL-Befehle zurückgeben
                ]);
            } catch (\Exception $e) {
                Yii::error('Fehler bei der SQL-Ausführung: ' . $e->getMessage(), __METHOD__);
                Yii::error('Daten: ' . print_r($data, true), __METHOD__);return $this->asJson(['success' => false, 'message' => 'Fehler beim Aktualisieren der Daten']);
            }
        }
        
        return $this->asJson(['success' => false, 'message' => 'Ungültige Anfrage']);
    }
    
}
