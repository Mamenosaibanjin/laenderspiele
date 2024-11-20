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
            [['wettbewerbtypID'], 'integer'], // Wettbewerbstyp-ID als Integer
            [['jahr'], 'integer', 'min' => 0], // Jahr als positive Ganzzahl
            [['land'], 'string', 'max' => 3], // Dreistelliger IOC-Code für Länder, kann NULL sein
            [['wettbewerbtypID'], 'required'], // Wettbewerbstyp ist erforderlich
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    public function getWettbewerbTyp()
    {
        return $this->hasOne(WettbewerbTyp::class, ['id' => 'wettbewerbtypID']); // Relation zu WettbewerbTyp
    }
}
?>