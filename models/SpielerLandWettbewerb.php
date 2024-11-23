<?php

namespace app\models;

use yii\db\ActiveRecord;

class SpielerLandWettbewerb extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'spieler_land_wettbewerb'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['spielerID', 'wettbewerbID', 'landID', 'positionID', 'jahr'], 'required'],
            [['spielerID', 'wettbewerbID', 'landID', 'positionID'], 'integer'],
            [['jahr'], 'integer'], // YYYY
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getSpieler()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spielerID']);
    }
    
    public function getWettbewerb()
    {
        return $this->hasOne(Wettbewerb::class, ['id' => 'wettbewerbID']);
    }
    
    public function getLand()
    {
        return $this->hasOne(Club::class, ['id' => 'landID']);
    }
    
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'positionID']);
    }
}
?>