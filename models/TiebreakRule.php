<?php
namespace app\models;

use yii\db\ActiveRecord;

class TiebreakRule extends ActiveRecord
{
    public static function tableName()
    {
        return 'tiebreak_rule';
    }
    
    public function rules()
    {
        return [
            [['tournament_id', 'tiebreak_type_id', 'rule_order'], 'required'],
            [['tournament_id', 'tiebreak_type_id', 'rule_order'], 'integer'],
            [['tournament_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Tournament::class, 'targetAttribute' => ['tournament_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TiebreakType::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }
    
    public function getTournament()
    {
        return $this->hasOne(Tournament::class, ['id' => 'tournament_id']);
    }
    
    public function gettiebreakType()
    {
        return $this->hasOne(TiebreakType::class, ['id' => 'tiebreak_type_id']);
    }
    
}
