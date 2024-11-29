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
    
    public function getRecentMatches($limit = 5)
    {
        return Spiel::find()
        ->joinWith('turnier') // Verknüpft mit Turnier für das Datum
        ->where(['or', ['club1ID' => $this->id], ['club2ID' => $this->id]]) // Spiele des Clubs
        ->orderBy(['turnier.datum' => SORT_DESC]) // Sortiert nach Datum absteigend
        ->limit($limit) // Begrenzung auf die letzten $limit Spiele
        ->all();
    }
    
    public function getSquad($clubID)
    {
        $currentYear = date('Y'); // Aktuelles Jahr
        
        // Spieler basierend auf den Bedingungen laden
        return Spieler::find()
        ->select(['spieler.name', 'spieler.vorname', 'spieler.id', 'spieler.nati1']) // Nur die gewünschten Spalten auswählen
        ->joinWith(['vereinSaison' => function ($query) {
            $query->alias('spieler_verein_saison'); // Alias explizit setzen
        }])
        ->where([
            'spieler_verein_saison.vereinID' => $clubID, // Club-ID
            'spieler_verein_saison.jugend' => 0, // Keine Jugendspieler
        ])
        ->andWhere([
            '<', 'spieler_verein_saison.von', ($currentYear + 1) . '07', // Startdatum
        ])
        ->andWhere([
            '>=', 'spieler_verein_saison.bis', $currentYear . '06', // Enddatum
        ])
        ->all();
    }
    
}
?>