<?php
namespace app\models;

use yii\db\ActiveRecord;

class TiebreakType extends ActiveRecord
{
    public static function tableName()
    {
        return 'tiebreak_type';
    }
    
    public function rules()
    {
        return [
            [['code', 'description'], 'required'],
            [['code'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['code'], 'unique'], // falls jeder Code nur einmal existieren darf
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'description' => 'Beschreibung',
        ];
    }
    
    /**
     * Alle Regeln, die diesen Typ verwenden
     */
    public function getTiebreakRules()
    {
        return $this->hasMany(TiebreakRule::class, ['tiebreak_type_id' => 'id']);
    }
}
