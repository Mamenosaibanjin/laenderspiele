<?php
namespace app\models;

use yii\db\ActiveRecord;

class TiebreakDrawing extends ActiveRecord
{
    public static function tableName()
    {
        return 'tiebreak_drawing';
    }
    
    public function rules()
    {
        return [
            [['tournament_id', 'runde_id', 'points', 'club_id', 'draw_order'], 'required'],
            [['tournament_id', 'runde_id', 'points', 'club_id', 'draw_order'], 'integer'],
        ];
    }
    
    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'club_id']);
    }
    
    public function getTournament()
    {
        return $this->hasOne(Tournament::class, ['id' => 'tournament_id']);
    }
}
