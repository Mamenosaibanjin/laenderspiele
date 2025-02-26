<?php
namespace app\models;

use yii\db\ActiveRecord;

class Tournament extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tournament';
    }
    
    public function rules()
    {
        return [
            [['wettbewerbID', 'jahr', 'land', 'startdatum'], 'required'],
            [['parentTournamentID'], 'integer'],
            [['startdatum'], 'date', 'format' => 'php:Y-m-d'],
            [['wettbewerbID'], 'exist', 'skipOnError' => true, 'targetClass' => Wettbewerb::class, 'targetAttribute' => ['wettbewerbID' => 'id']],
            [['parentTournamentID'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::class, 'targetAttribute' => ['parentTournamentID' => 'id']],
        ];
    }
    
    public function getParentTournament()
    {
        return $this->hasOne(Tournament::class, ['id' => 'parentTournamentID']);
    }
    
    public function getSubTournaments()
    {
        return $this->hasMany(Tournament::class, ['parentTournamentID' => 'id']);
    }
}

?>