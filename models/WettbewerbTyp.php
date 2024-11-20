<?php

namespace app\models;

use yii\db\ActiveRecord;

class WettbewerbTyp extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'wettbewerbTyp'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255], // Name des Typs (max. 255 Zeichen)
            [['name'], 'required'], // Name des Wettbewerbstyps ist erforderlich
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getWettbewerbe()
    {
        return $this->hasMany(Wettbewerb::class, ['wettbewerbtypID' => 'id']); // Relation zu mehreren Wettbewerben
    }
}
?>