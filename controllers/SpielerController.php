<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\models\Club;
use app\models\Position;
use app\models\Spiel;
use app\models\Spieler;
use app\models\SpielerVereinSaison;
use app\models\SpielerLandWettbewerb;
use Yii;
use http\Url;
use app\models\Wettbewerb;

class SpielerController extends Controller
{
    public function actionView($id = 0)
    {
        $isEditing = !Yii::$app->user->isGuest; // Bearbeitungsmodus nur für eingeloggte Benutzer
        
        // Spieler-Daten laden oder neues Modell erstellen
        $spieler = $id ? Spieler::findOne($id) : new Spieler();
        
        if (!$spieler && $id) {
            throw new \yii\web\NotFoundHttpException('Der angeforderte Spieler wurde nicht gefunden.');
        }
        
        // Vereinslisten laden
        $vereine = Club::find()
        ->andWhere(['typID' => [3, 4, 5, 6, 7, 8, 9, 10, 13]])
        ->orderBy('name')
        ->all();
        
        $nationen = Club::find()
        ->andWhere(['typID' => [1, 2, 11, 12]])
        ->orderBy('name')
        ->all();
        
        // Weitere Daten laden
        $positionen = Position::find()
        ->orderBy('positionKurz')
        ->all();
        
        $wettbewerbe = Wettbewerb::find()
        ->orderBy('name')
        ->all();
        
        // Karriere-Daten laden
        $vereinsKarriere = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 0])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        $jugendvereine = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 1])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        $laenderspiele = SpielerLandWettbewerb::find()
        ->where(['spielerID' => $id])
        ->orderBy(['jahr' => SORT_DESC])
        ->all();
        
        // Bearbeitungsmodus: Daten speichern
        if ($isEditing && Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            
            // Spieler-Daten laden und speichern
            if ($spieler->load($request->post()) && $spieler->save()) {
                Yii::$app->session->setFlash('success', 'Die Spielerdaten wurden erfolgreich gespeichert.');
                return $this->redirect(['spieler/view', 'id' => $spieler->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Fehler beim Speichern der Spielerdaten.');
            }
        }
        
        // View rendern
        return $this->render('view', [
            'spieler' => $spieler,
            'vereinsKarriere' => $vereinsKarriere,
            'jugendvereine' => $jugendvereine,
            'laenderspiele' => $laenderspiele,
            'vereine' => $vereine,
            'nationen' => $nationen,
            'positionen' => $positionen,
            'wettbewerbe' => $wettbewerbe,
            'isEditing' => $isEditing,
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
    

    public function actionSaveDetails()
    {
        $request = Yii::$app->request;
        $data = Yii::$app->request->post();
        if ($request->isPost && $data) {
            $playerID = $data['playerID'];
            $player = Spieler::findOne($playerID);
            if (!$player) {
                $player = new Spieler();
            }
           
            // Spieler-Daten aktualisieren
            $player->name = $data['Spieler']['name'];
            $player->vorname = $data['Spieler']['vorname'];
            $player->fullname = $data['Spieler']['fullname'];
            $player->geburtstag = $data['Spieler']['geburtstag'];
            $player->geburtsort = $data['Spieler']['geburtsort'];
            $player->geburtsland = $data['Spieler']['geburtsland'];
            $player->nati1 = $data['Spieler']['nati1'];
            $player->nati2 = $data['Spieler']['nati2'];
            $player->nati3 = $data['Spieler']['nati3'];
            $player->weight = $data['Spieler']['weight'];
            $player->height = $data['Spieler']['height'];
            $player->spielfuss = $data['Spieler']['spielfuss'];
            $player->homepage = $data['Spieler']['homepage'];
            $player->facebook = $data['Spieler']['facebook'];
            $player->instagram = $data['Spieler']['instagram'];
            
            if ($player->load($data) && $player->save()) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Speichern erfolgreich',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Speichern fehlgeschlagen',
                    'errors' => $player->errors, // Gibt Validierungsfehler zurück
                ]);
            }
        }
        
        return $this->asJson(['success' => false, 'message' => 'Ungültige Anfrage']);
    }
    
    public function actionSaveNation()
    {
        $request = Yii::$app->request;
        $data = json_decode($request->getRawBody(), true);
        Yii::info($data, 'debug'); // Debugging: Log-Daten ausgeben
        
        if ($request->isPost && $data) {
            try {
                // Validierung der Eingabedaten
                if (empty($data['spielerID']) || empty($data['wettbewerbID']) || empty($data['landID']) || empty($data['positionID']) || empty($data['jahr'])) {
                    return $this->asJson(['success' => false, 'message' => 'Fehlende Daten']);
                }
                
                // Zerlegen der data-id in die ursprünglichen Werte
                list($spielerID, $landID, $wettbewerbID, $land, $jahr) = explode('-', $data['dataId']);
                
                // Prüfen, ob ein Datensatz existiert (Vergleich aller Felder außer ID)
                $existingEntry = SpielerLandWettbewerb::findOne([
                    'spielerID' => $spielerID,
                    'wettbewerbID' => $wettbewerbID,
                    'landID' => $landID,
                    'jahr' => $jahr,
                    'land' => $land
                ]);
                \Yii::info($data, 'debug');
                if ($existingEntry) {
                    // Update des bestehenden Eintrags
                    $existingEntry->spielerID = $data['spielerID'];
                    $existingEntry->wettbewerbID = $data['wettbewerbID'];
                    $existingEntry->landID = $data['landID'];
                    $existingEntry->positionID = $data['positionID'];
                    $existingEntry->jahr = $data['jahr'];
                    $existingEntry->land = $data['land'];
                    
                    if ($existingEntry->save()) {
                        return $this->asJson(['success' => true, 'message' => 'Daten erfolgreich aktualisiert']);
                    } else {
                        Yii::error($existingEntry->errors, 'debug'); // Debugging: Validierungsfehler ausgeben
                        return $this->asJson(['success' => false, 'message' => 'Fehler beim Aktualisieren']);
                    }
                } else {
                    $spielerNation = new SpielerLandWettbewerb(); // Erstellen eines neuen Eintrags
                    $spielerNation->spielerID = $data['spielerID'];
                    $spielerNation->wettbewerbID = $data['wettbewerbID'];
                    $spielerNation->landID = $data['landID'];
                    $spielerNation->positionID = $data['positionID'];
                    $spielerNation->jahr = $data['jahr'];
                    
                    if ($spielerNation->save()) {
                        return $this->asJson(['success' => true, 'message' => 'Daten erfolgreich gespeichert']);
                    } else {
                        Yii::error($spielerNation->errors, 'debug'); // Debugging: Validierungsfehler ausgeben
                        return $this->asJson(['success' => false, 'message' => 'Fehler beim Speichern']);
                    }
                }
            } catch (\Exception $e) {
                Yii::error('Fehler beim Speichern: ' . $e->getMessage());
                return $this->asJson(['success' => false, 'message' => 'Ein interner Fehler ist aufgetreten']);
            }
        }
        
        return $this->asJson(['success' => false, 'message' => 'Ungültige Anfrage']);
    }
    

    public function actionSaveClub()
    {
        $request = Yii::$app->request;
        $data = json_decode($request->getRawBody(), true);
        Yii::info($data, 'debug'); // Debugging: Log-Daten ausgeben
        
        if ($request->isPost && $data) {
            try {
                // Validierung der Eingabedaten
                if (empty($data['spielerID']) || empty($data['vereinID']) || empty($data['positionID'])) {
                    return $this->asJson(['success' => false, 'message' => 'Fehlende Daten']);
                }
                
                $existingEntry = SpielerVereinSaison::findOne(['id' => $data['dataId']]);
                
                \Yii::info($data, 'debug');
                if ($existingEntry) {
                    // Update des bestehenden Eintrags
                    $existingEntry->spielerID = $data['spielerID'];
                    $existingEntry->vereinID = $data['vereinID'];
                    $existingEntry->von = (int) str_replace('-', '', $data['von']);;
                    $existingEntry->bis = (int) str_replace('-', '', $data['bis']);;
                    $existingEntry->positionID = $data['positionID'];
                    $existingEntry->jugend = 0;
                    
                    if ($existingEntry->save()) {
                        return $this->asJson(['success' => true, 'message' => 'Daten erfolgreich aktualisiert']);
                    } else {
                        Yii::error($existingEntry->errors, 'debug'); // Debugging: Validierungsfehler ausgeben
                        return $this->asJson(['success' => false, 'message' => 'Fehler beim Aktualisieren']);
                    }
                } else {
                    $spielerNation = new SpielerLandWettbewerb(); // Erstellen eines neuen Eintrags
                    $spielerNation->spielerID = $data['spielerID'];
                    $spielerNation->vereinID = $data['vereinID'];
                    $spielerNation->von = (int) str_replace('-', '', $data['von']);;
                    $spielerNation->bis = (int) str_replace('-', '', $data['bis']);;
                    $spielerNation->positionID = $data['positionID'];
                    $spielerNation->jugend = 0;
                    
                    if ($spielerNation->save()) {
                        return $this->asJson(['success' => true, 'message' => 'Daten erfolgreich gespeichert']);
                    } else {
                        Yii::error($spielerNation->errors, 'debug'); // Debugging: Validierungsfehler ausgeben
                        return $this->asJson(['success' => false, 'message' => 'Fehler beim Speichern']);
                    }
                }
            } catch (\Exception $e) {
                Yii::error('Fehler beim Speichern: ' . $e->getMessage());
                return $this->asJson(['success' => false, 'message' => 'Ein interner Fehler ist aufgetreten']);
            }
        }
        
        return $this->asJson(['success' => false, 'message' => 'Ungültige Anfrage']);
    }
    
    public function actionReloadCareerTable($spielerId)
    {
        $vereinsKarriere = SpielerVereinSaison::find()
        ->where(['spielerID' => $spielerId, 'jugend' => 0])
        ->orderBy(['von' => SORT_DESC])
        ->all(); // Daten aus der Datenbank
        
        // Zusätzliche Daten, die von der Partial-View benötigt werden
        $vereine = Club::find()->all(); // Passe dies an deine Logik an
        $positionen = Position::find()->all(); // Passe dies an deine Logik an
        $currentMonth = date('Ym'); // Aktueller Monat im benötigten Format
        $isEditing = false; // Standardmäßig nicht im Bearbeitungsmodus
        
        // Partial-View zurückgeben
        return $this->renderPartial('_career_table', [
            'vereinsKarriere' => $vereinsKarriere,
            'isEditing' => $isEditing,
            'currentMonth' => $currentMonth,
            'vereine' => $vereine,
            'positionen' => $positionen,
        ]);
    }
    

}

?>