<?php

namespace app\models;

use yii\db\ActiveRecord;

class SpielerVereinSaison extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'spieler_verein_saison'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['spielerID', 'vereinID', 'positionID'], 'required'],
            [['spielerID', 'vereinID', 'positionID'], 'integer'],
            [['von', 'bis'], 'safe'], // Markiere die Felder als sicher für Eingaben
            [['jugend'], 'boolean'],
        ];
    }
    
    public function beforeSave($insert)
    {
        if (!empty($this->von)) {
            $this->von = str_replace('-', '', $this->von); // YYYY-MM zu YYYYMM
        }
        if (!empty($this->bis)) {
            $this->bis = str_replace('-', '', $this->bis); // YYYY-MM zu YYYYMM
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getSpieler()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spielerID']);
    }
    
    public function getVerein()
    {
        return $this->hasOne(Club::class, ['id' => 'vereinID']);
    }
    
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'positionID']);
    }
}
?>