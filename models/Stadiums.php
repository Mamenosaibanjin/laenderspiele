<?php

namespace app\models;

use yii\db\ActiveRecord;

class Stadiums extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'stadiums'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['name', 'stadt', 'land'], 'required'], // Name, Stadt und Land sind Pflichtfelder
            [['name', 'stadt'], 'string', 'max' => 255], // Name und Stadt: max. 255 Zeichen
            [['land'], 'string', 'max' => 3], // Land: Dreistelliger IOC-Code
            [['kapazitaet'], 'integer', 'min' => 0], // Kapazität: Positive Ganzzahl oder NULL
            [['kapazitaet'], 'default', 'value' => null], // Standardwert für Kapazität ist NULL
        ];
    }
    
    /**
     * Labels für die Attribute.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Stadionname',
            'stadt' => 'Stadt',
            'land' => 'Land (IOC-Code)',
            'kapazitaet' => 'Kapazität',
        ];
    }

    public static function getZufallsId()
    {
        $query = Stadiums::find()
        ->select(['id']) // Spalten auswählen
        ->orderBy(['rand()' => SORT_DESC]) // Sortieren
        ->limit(1)
        ->all();
        
        return $query[0]['id'];
    }
    
}
?>