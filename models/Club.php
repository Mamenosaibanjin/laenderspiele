<?php

namespace app\models;

use app\models\Spiel;
use yii\db\ActiveRecord;

class Club extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'clubs'; // Tabellenname
    }
    
    public static function primaryKey()
    {
        return ['id'];
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name', 'namevoll', 'farben', 'strasse', 'postfach', 'ort', 'telefon', 'telefax', 'homepage', 'email'], 'string', 'max' => 255],
            [['typID', 'nachfolgerID', 'stadiumID'], 'integer'], // IDs als Integer
            [['land'], 'string', 'max' => 3], // Dreistelliger IOC-Code
            [['founded'], 'string', 'max' => 255], // Unterstützt verschiedene Datumsformate
            [['homepage'], 'url'], // URL-Validierung
            [['email'], 'email'], // E-Mail-Validierung
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getTyp()
    {
        return $this->hasOne(ClubTyp::class, ['id' => 'typID']); // Relation zu Club-Typen
    }
    
    public function getNachfolger()
    {
        return $this->hasOne(Club::class, ['id' => 'nachfolgerID']); // Selbstreferenz für Nachfolger
    }
    
    public function getStadion()
    {
        return $this->hasOne(Stadion::class, ['id' => 'stadionID']) // Relation zu Stadium
        ->alias('stadion'); // Alias für die Tabelle
    }
    
    public function getNation()
    {
        return $this->hasOne(Nation::class, ['kuerzel' => 'land']);
    }
    
    public function getRecentMatches()
    {
        return Spiel::find()
        ->alias('s') // Alias für Spiele-Tabelle
        ->leftJoin('turnier t', 's.id = t.spielID') // Verknüpfung mit Turnier
        ->where(['or', ['s.club1ID' => $this->id], ['s.club2ID' => $this->id]]) // Bedingung korrekt setzen
        ->andWhere(['<=', 't.datum', new \yii\db\Expression('NOW()')]) // Datum kleiner oder gleich der aktuellen Zeit
        ->select(['s.*', 't.*']) // Spalten auswählen
        ->orderBy(['t.datum' => SORT_DESC]) // Sortieren
        ->limit(5)
        ->all();
    }
    
    public function getUpcomingMatches($limit = 5)
    {
        return Spiel::find()
        ->alias('s') // Alias für Spiele-Tabelle
        ->leftJoin('turnier t', 's.id = t.spielID') // Verknüpfung mit Turnier
        ->where(['or', ['s.club1ID' => $this->id], ['s.club2ID' => $this->id]]) // Bedingung korrekt setzen
        ->andWhere(['>=', 't.datum', new \yii\db\Expression('NOW()')]) // Datum kleiner oder gleich der aktuellen Zeit
        ->select(['s.*', 't.*']) // Spalten auswählen
        ->orderBy(['t.datum' => SORT_ASC]) // Sortieren
        ->limit(5)
        ->all();
    }
    
    public function getLastMatch()
    {
        return Spiel::find()
        ->alias('s')
        ->leftJoin('turnier t', 's.id = t.spielID')
        ->where(['or', ['s.club1ID' => $this->id], ['s.club2ID' => $this->id]])
        ->andWhere(['<=', 't.datum', new \yii\db\Expression('NOW()')])
        ->select(['wettbewerbID' => 't.wettbewerbID', 'jahr' => 't.jahr'])
        ->orderBy(['t.datum' => SORT_DESC])
        ->asArray() // Rückgabe als Array
        ->one(); // Nur ein Ergebnis zurückgeben
    }
    
    public function getSquad($clubID, $year = null)
    {
        $currentYear = $year ?? date('Y');
        
        $query = Spieler::find()
        ->select([
            'spieler.name',
            'spieler.vorname',
            'spieler.id',
            'spieler.nati1',
            'spieler.geburtstag',
            'spieler_verein_saison.positionID',
        ])
        ->joinWith(['vereinSaison' => function ($query) use ($clubID, $currentYear) {
            $query->alias('spieler_verein_saison')
            ->andWhere(['spieler_verein_saison.vereinID' => $clubID])
            ->andWhere(['<', 'spieler_verein_saison.von', ($currentYear + 1) . '07'])
            ->andWhere(['>=', 'spieler_verein_saison.bis', $currentYear . '06']);
        }])
        ->where(['spieler_verein_saison.vereinID' => $clubID])
        ->andWhere(['spieler_verein_saison.jugend' => 0])
        ->orderBy([
            'spieler_verein_saison.positionID' => SORT_ASC,
            'spieler.name' => SORT_ASC,
        ]);

        return $query->all();
    }
    

    public function getNationalSquad($clubID)
    {
        // Werte von getLastMatch holen
        $lastMatch = $this->getLastMatch();
        
        if (!$lastMatch) {
            // Keine Spiele gefunden, leere Sammlung zurückgeben
            return [];
        }
        
        $wettbewerbID = $lastMatch['wettbewerbID'];
        $jahr = $lastMatch['jahr'];
        // Spieler basierend auf den Bedingungen laden
        return Spieler::find()
        ->select(['spieler.name', 'spieler.vorname', 'spieler.id', 'spieler.nati1', 'spieler_land_wettbewerb.positionID']) // Nur die gewünschten Spalten auswählen
        ->joinWith(['landWettbewerb' => function ($query) {
            $query->alias('spieler_land_wettbewerb'); // Alias explizit setzen
        }])
        ->where([
            'spieler_land_wettbewerb.landID' => $clubID, // Club-ID
        ])
        ->andWhere([
            'spieler_land_wettbewerb.wettbewerbID' => $wettbewerbID,
        ])
        ->andWhere([
            'spieler_land_wettbewerb.jahr' => $jahr,
        ])
        ->all();
    }
    
}
?>