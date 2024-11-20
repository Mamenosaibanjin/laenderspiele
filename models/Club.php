<?php

namespace app\models;

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
    
    public function getStadium()
    {
        return $this->hasOne(Stadiums::class, ['id' => 'stadiumID']); // Relation zu Stadium
    }
}
?>