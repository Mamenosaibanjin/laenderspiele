<?php
namespace app\models;

use yii\db\ActiveRecord;

class Stadion extends ActiveRecord
{
    /**
     * Gibt den Namen der Tabelle zurück.
     */
    public static function tableName()
    {
        return 'stadiums';
    }
    
    public function rules()
    {
        return [
            [['name', 'stadt', 'land'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['stadt'], 'string', 'max' => 255],
            [['land'], 'string', 'max' => 3],
            [['kapazitaet'], 'integer'],
        ];
    }
}
?>