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
            [['club1ID', 'club2ID', 'tore1', 'tore2', 'extratime', 'penalty', 'turnierID'], 'integer'], // Zahlenwerte
            [['club1ID', 'club2ID'], 'exist', 'targetClass' => Club::class, 'targetAttribute' => 'id'], // Prüfung auf Existenz in der Club-Tabelle
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    
    // Beziehung zu Club 1
    public function getClub1()
    {
        return $this->hasOne(Club::class, ['id' => 'club1ID']);
    }
    
    // Beziehung zu Club 2
    public function getClub2()
    {
        return $this->hasOne(Club::class, ['id' => 'club2ID']);
    }
    
    // Beziehung zu Turnier
    public function getTurnier()
    {
        return $this->hasOne(Turnier::class, ['spielID' => 'id']);
    }
}
?>