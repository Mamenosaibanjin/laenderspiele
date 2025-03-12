<?php

namespace app\models;

use Yii;
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
            [['spielerID', 'positionID'], 'required'],
            [['spielerID', 'wettbewerbID', 'tournamentID', 'landID', 'positionID'], 'integer'],
            [['jahr'], 'integer'], // YYYY
            
            // Entweder `wettbewerbID` oder `tournamentID` muss gesetzt sein, aber nicht beide gleichzeitig
            ['wettbewerbID', 'validateCompetition'],
            ['landID', 'validateDuplicate'], // Dubletten-Prüfung aktivieren
            
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
    
    /**
     * Custom-Validierung für `wettbewerbID` und `tournamentID`
     */
    public function validateCompetition($attribute, $params)
    {
        if (empty($this->wettbewerbID) && empty($this->tournamentID)) {
            $this->addError($attribute, 'Entweder Wettbewerb oder Turnier muss gesetzt sein.');
        }
        
        if (!empty($this->wettbewerbID) && !empty($this->tournamentID)) {
            $this->addError($attribute, 'Es darf nicht gleichzeitig ein Wettbewerb und ein Turnier gesetzt sein.');
        }
    }
    
    public function validateDuplicate($attribute, $params)
    {
        $query = self::find()
        ->where([
            'spielerID' => $this->spielerID,
            'landID' => $this->landID,
            'tournamentID' => $this->tournamentID,
            'jahr' => $this->jahr,
        ]);
        
        // Falls es sich um einen bestehenden Eintrag handelt, den aktuellen ausschließen
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'id', $this->id]);
        }
        
        if ($query->exists()) {
            $this->addError($attribute, 'Eintrag existiert bereits für dieses Turnier oder Jahr.');
        }
    }
    
}
?>