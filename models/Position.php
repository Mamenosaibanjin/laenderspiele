<?php

namespace app\models;

use yii\db\ActiveRecord;

class Position extends ActiveRecord
{
    /**
     * Gibt den Tabellen-Namen zurück.
     */
    public static function tableName()
    {
        return 'position'; // Tabellenname
    }
    
    /**
     * Validierungsregeln für das Model.
     */
    public function rules()
    {
        return [
            [['positionKurz'], 'string', 'max' => 10], // Kürzel (z.B. TW, MF)
            [['positionLang_de'], 'string', 'max' => 255], // Langform auf Deutsch
            [['positionKurz', 'positionLang_de'], 'required'], // Beide Felder sind erforderlich
        ];
    }
}
?>