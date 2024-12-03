<?php

namespace app\models;

use yii\db\ActiveRecord;

class Spieler extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'spieler'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name', 'vorname', 'fullname', 'geburtsort', 'geburtsland'], 'string'],
            [['geburtstag'], 'date', 'format' => 'php:Y-m-d'], // Date im Format YYYY-MM-DD
            [['height', 'weight'], 'number'],
            [['spielfuss'], 'string', 'max' => 1], // Max. 1 Zeichen (z.B. 'L' oder 'R')
            [['homepage', 'facebook', 'instagram'], 'url'],
            [['nati1', 'nati2', 'nati3'], 'string', 'max' => 3], // IOC-Code max. 3 Zeichen
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getVereinSaison()
    {
        return $this->hasMany(SpielerVereinSaison::class, ['spielerID' => 'id']);
    }
    
    public function getFilteredVereinSaison($clubID, $year = null)
    {
        $query = $this->getVereinSaison()->andWhere(['vereinID' => $clubID]);
        
        if ($year !== null) {
            $query->andWhere(['<', 'von', ($year + 1) . '07'])
            ->andWhere(['>=', 'bis', $year . '06']);
        }
        
        return $query;
    }
    
    public function getLandWettbewerb()
    {
        return $this->hasMany(SpielerLandWettbewerb::class, ['spielerID' => 'id']);
    }
    
    
}
?>