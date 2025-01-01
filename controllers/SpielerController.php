<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Spiel;
use app\models\Spieler;
use app\models\SpielerVereinSaison;
use app\models\SpielerLandWettbewerb;
use Yii;

class SpielerController extends Controller
{
    public function actionView($id)
    {
        // Spieler-Daten
        $spieler = Spieler::findOne($id);
        
        // Vereins-Karriere
        $vereinsKarriere = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 0])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        // Jugendvereine
        $jugendvereine = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 1])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        // Länderspiel-Karriere
        $laenderspiele = SpielerLandWettbewerb::find()
        ->where(['spielerID' => $id])
        ->orderBy(['jahr' => SORT_DESC])
        ->all();
        
        return $this->render('view', [
            'spieler' => $spieler,
            'vereinsKarriere' => $vereinsKarriere,
            'jugendvereine' => $jugendvereine,
            'laenderspiele' => $laenderspiele,
        ]);
    }
    
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $term = Yii::$app->request->get('term');
        $spieler = Spieler::find()
        ->select(['id', 'fullname as value']) // 'value' ist erforderlich für jQuery UI
        ->where(['like', 'name', $term])
        ->asArray()
        ->all();
        
        return $spieler;
    }

    public function actionSearchForLineup($spielID, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $term = Yii::$app->request->get('term');
        
        // Hole die relevanten Informationen aus der Tabelle 'spiele'
        $spiel = Spiel::findOne($spielID);
        
        if (!$spiel) {
            return ['error' => 'Spiel nicht gefunden.'];
        }
        
        // Bestimme, welches ClubID-Feld (Heim oder Auswärts) genutzt wird
        $clubID = ($type === 'H') ? $spiel->club1ID : $spiel->club2ID;
        
        // Finde Spieler basierend auf den Bedingungen
        $spieler = Spieler::find()
        ->alias('s') // Kürzel für die Tabelle 'spieler'
        ->select(['s.id', 's.fullname as value']) // Wähle die ID und den Namen des Spielers aus
        ->innerJoin('spieler_land_wettbewerb slw', 'slw.spielerID = s.id') // Verknüpfe 'spieler_land_wettbewerb'
        ->innerJoin('turnier t', 't.wettbewerbID = slw.wettbewerbID AND t.jahr = slw.jahr') // Verknüpfe 'turnier'
        ->where([
            'or',
            ['like', 's.name', $term],      // Suche nach Name
            ['like', 's.vorname', $term],  // Suche nach Vorname
            ['like', 's.fullname', $term]  // Suche nach Vollname
        ])// Bedingung für den Namen
        ->andWhere(['t.spielID' => $spielID]) // Bedingung für turnier.spielID
        ->andWhere(['slw.landID' => $clubID]) // Bedingung für spieler_land_wettbewerb.landID
        ->asArray()
        ->all();
        
        return $spieler;
    }
    

    public function actionSave()
    {
        $request = Yii::$app->request;
        $data = json_decode($request->getRawBody(), true);
        
        \Yii::info("SPieler-ID:", 'debug'); // Für die Debug-Ausgabe in den Logs
        if ($request->isPost && $data) {
            try {
                $playerID = $data['playerID'];
                $player = Spieler::findOne($playerID);
                if (!$player) {
                    return $this->asJson(['success' => false, 'message' => 'Spieler nicht gefunden']);
                }
                
                // Spieler-Daten aktualisieren
                $player->name = $data['name'];
                $player->vorname = $data['vorname'];
                $player->fullname = $data['fullname'];
                $player->geburtstag = $data['geburtstag'];
                $player->geburtsort = $data['geburtsort'];
                $player->geburtsland = $data['geburtsland'];
                $player->nati1 = $data['nati1'];
                $player->nati2 = $data['nati2'];
                $player->nati3 = $data['nati3'];
                $player->weight = $data['weight'];
                $player->height = $data['height'];
                $player->spielfuss = $data['spielfuss'];
                $player->homepage = $data['homepage'];
                $player->facebook = $data['facebook'];
                $player->instagram = $data['instagram'];

                if ($player->save()) {
                    return $this->asJson(['success' => true]);
                } else {
                    return $this->asJson(['success' => false, 'message' => 'Speichern fehlgeschlagen']);
                }
            } catch (\Exception $e) {
                Yii::error('Fehler beim Speichern des Spielers: ' . $e->getMessage());
                return $this->asJson(['success' => false, 'message' => 'Fehler beim Speichern']);
            }
        }
        
        return $this->asJson(['success' => false, 'message' => 'Ungültige Anfrage']);
    }
}

?>