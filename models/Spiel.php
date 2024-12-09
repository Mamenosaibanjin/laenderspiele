<?php 
namespace app\models;

use yii\db\ActiveRecord;

class Spiel extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'spiele'; // Name der Tabelle in der Datenbank
    }

    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['club1ID', 'club2ID', 'tore1', 'tore2', 'extratime', 'penalty', 'turnierID', 'aufstellung1ID', 'aufstellung2ID', 'stadiumID', 'zuschauer', 'referee1ID', 'referee2ID', 'referee3ID', 'referee4ID'], 'integer'], // Zahlenwerte
            [['club1ID', 'club2ID'], 'exist', 'targetClass' => Club::class, 'targetAttribute' => 'id'], // Prüfung auf Existenz in der Club-Tabelle
            [['aufstellung1ID', 'aufstellung2ID'], 'exist', 'targetClass' => Aufstellung::class, 'targetAttribute' => 'id'], // Prüfung auf Existenz in der Aufstellung-Tabelle
            [['stadiumID'], 'exist', 'targetClass' => Stadiums::class, 'targetAttribute' => 'id'], // Prüfung auf Existenz in der Stadium-Tabelle
            [['referee1ID', 'referee2ID', 'referee3ID', 'referee4ID'], 'exist', 'targetClass' => Referee::class, 'targetAttribute' => 'id'], // Prüfung auf Existenz in der Referee-Tabelle
        ];
    }

    /**
     * Relationen zu anderen Tabellen.
     */
    public function getClub1()
    {
        return $this->hasOne(Club::class, ['id' => 'club1ID']);
    }
    
    public function getClub2()
    {
        return $this->hasOne(Club::class, ['id' => 'club2ID']);
    }
    
    public function getHeimClub()
    {
        return $this->club1; // Gibt den gleichen Wert wie 'getClub1()'
    }
    
    public function getAuswaertsClub()
    {
        return $this->club2; // Gibt den gleichen Wert wie 'getClub2()'
    }
    
    public function getTurnier()
    {
        return $this->hasOne(Turnier::class, ['spielID' => 'id']);
    }

    // Neue Relationen für die neuen Felder
    public function getAufstellung1()
    {
        return $this->hasOne(Aufstellung::class, ['id' => 'aufstellung1ID']);
    }

    public function getAufstellung2()
    {
        return $this->hasOne(Aufstellung::class, ['id' => 'aufstellung2ID']);
    }

    public function getStadium()
    {
        return $this->hasOne(Stadiums::class, ['id' => 'stadiumID']);
    }

    public function getReferee1()
    {
        return $this->hasOne(Referee::class, ['id' => 'referee1ID']);
    }

    public function getReferee2()
    {
        return $this->hasOne(Referee::class, ['id' => 'referee2ID']);
    }

    public function getReferee3()
    {
        return $this->hasOne(Referee::class, ['id' => 'referee3ID']);
    }

    public function getReferee4()
    {
        return $this->hasOne(Referee::class, ['id' => 'referee4ID']);
    }
    
    /**
     * Prüft, ob eine Aktion einer Heimmannschaft zugeordnet ist.
     */
    public function isHeimAktion($spielerID)
    {
        if (!$spielerID || !$this->aufstellung1) {
            echo "heim 1";
            return false;
        }
        
        // IDs der Heimspieler (Startaufstellung)
        $heimSpielerIDs = [
            $this->aufstellung1->spieler1ID,
            $this->aufstellung1->spieler2ID,
            $this->aufstellung1->spieler3ID,
            $this->aufstellung1->spieler4ID,
            $this->aufstellung1->spieler5ID,
            $this->aufstellung1->spieler6ID,
            $this->aufstellung1->spieler7ID,
            $this->aufstellung1->spieler8ID,
            $this->aufstellung1->spieler9ID,
            $this->aufstellung1->spieler10ID,
            $this->aufstellung1->spieler11ID,
        ];
        
        if (in_array($spielerID, $heimSpielerIDs, true)) {
            echo "Heim 2";
            return true;
        }
        
        // Prüfen, ob der Spieler über eine Wechselkette mit einem Heimspieler verbunden ist
        return $this->isEingewechselterHeimspieler($spielerID, $heimSpielerIDs);
    }
    
    private function isEingewechselterHeimspieler($spielerID, $heimSpielerIDs)
    {
        // Initiale Abfrage: Spieler wurde eingewechselt
        $wechselAktion = Games::find()
        ->select(['spielerID'])
        ->where(['spielID' => $this->id, 'aktion' => 'AUS', 'spieler2ID' => $spielerID])
        ->one();
        
        if (!$wechselAktion) {
            // Kein vorheriger Wechsel gefunden
            return false;
        }
        
        // SpielerID des Auswechselspielers abrufen
        $ausgewechselterSpielerID = $wechselAktion['spielerID'];
        
        // Prüfen, ob dieser Spieler in der Startaufstellung war
        if (in_array($ausgewechselterSpielerID, $heimSpielerIDs, true)) {
            return true;
        }
        
        // Rekursive Überprüfung für den ausgewechselten Spieler
        return $this->isEingewechselterHeimspieler($ausgewechselterSpielerID, $heimSpielerIDs);
    }
    
    
    /**
     * Prüft, ob eine Aktion einer Auswärtsmannschaft zugeordnet ist.
     */
    public function isAuswaertsAktion($spielerID)
    {
        if (!$spielerID || !$this->aufstellung2) {
            return false;
        }
        
        // IDs der Auswärtsspieler (Startaufstellung)
        $auswaertsSpielerIDs = [
            $this->aufstellung2->spieler1ID,
            $this->aufstellung2->spieler2ID,
            $this->aufstellung2->spieler3ID,
            $this->aufstellung2->spieler4ID,
            $this->aufstellung2->spieler5ID,
            $this->aufstellung2->spieler6ID,
            $this->aufstellung2->spieler7ID,
            $this->aufstellung2->spieler8ID,
            $this->aufstellung2->spieler9ID,
            $this->aufstellung2->spieler10ID,
            $this->aufstellung2->spieler11ID,
        ];
        
        if (in_array($spielerID, $auswaertsSpielerIDs, true)) {
            return true;
        }
        
        // Prüfen, ob der Spieler über eine Wechselkette mit einem Auswärtsspieler verbunden ist
        return $this->isEingewechselterAuswaertsspieler($spielerID, $auswaertsSpielerIDs);
    }
    
    private function isEingewechselterAuswaertsspieler($spielerID, $auswaertsSpielerIDs)
    {
        // Initiale Abfrage: Spieler wurde eingewechselt
        $wechselAktion = Games::find()
        ->select(['spielerID'])
        ->where(['spielID' => $this->id, 'aktion' => 'AUS', 'spieler2ID' => $spielerID])
        ->one();
        
        if (!$wechselAktion) {
            // Kein vorheriger Wechsel gefunden
            return false;
        }
        
        // SpielerID des Auswechselspielers abrufen
        $ausgewechselterSpielerID = $wechselAktion['spielerID'];
        
        // Prüfen, ob dieser Spieler in der Startaufstellung war
        if (in_array($ausgewechselterSpielerID, $auswaertsSpielerIDs, true)) {
            return true;
        }
        
        // Rekursive Überprüfung für den ausgewechselten Spieler
        return $this->isEingewechselterAuswaertsspieler($ausgewechselterSpielerID, $auswaertsSpielerIDs);
    }
    
}
?>