<?php

namespace app\models;

use yii\db\ActiveRecord;

class ClubTyp extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'clubTyp'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name_de'], 'required'], // Name auf Deutsch ist ein Pflichtfeld
            [['name_de'], 'string', 'max' => 255], // Name darf maximal 255 Zeichen enthalten
        ];
    }
    
    /**
     * Labels für die Attribute.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_de' => 'Typ (Deutsch)',
        ];
    }
    
    /**
     * Relationen.
     */
    public function getClubs()
    {
        return $this->hasMany(Club::class, ['typID' => 'id']); // Relation zu mehreren Clubs
    }
}
?>