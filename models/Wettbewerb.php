<?php

namespace app\models;

use yii\db\ActiveRecord;

class Wettbewerb extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'wettbewerb'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255], // Name ist ein String mit max. 255 Zeichen
            [['name'], 'required'], // Name ist erforderlich
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getTurniere()
    {
        return $this->hasMany(Turnier::class, ['wettbewerbID' => 'id']); // Relation zu Turnieren
    }
}
?>