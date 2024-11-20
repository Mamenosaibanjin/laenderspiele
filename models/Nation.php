<?php
namespace app\models;

use yii\db\ActiveRecord;

class Nation extends ActiveRecord
{
    /**
     * Gibt den Namen der Tabelle zurück.
     */
    public static function tableName()
    {
        return 'nation';
    }

    public function rules()
    {
        return [
            [['kuerzel', 'land_de', 'ISO3166'], 'required'],
            [['kuerzel'], 'string', 'max' => 3],
            [['land_de'], 'string', 'max' => 255],
            [['ISO3166'], 'string', 'max' => 2],
            [['kuerzel'], 'unique'],
            [['ISO3166'], 'unique'],
        ];
    }
}
?>