<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Games extends ActiveRecord
{
    public static function tableName()
    {
        return 'games';
    }
    
    public function rules()
    {
        return [
            [['spielID', 'minute', 'spielerID', 'spieler2ID'], 'integer'],
            [['aktion'], 'string', 'max' => 5],
            [['zusatz'], 'string', 'max' => 255],
            [['spielID'], 'exist', 'skipOnError' => true, 'targetClass' => Spiel::class, 'targetAttribute' => ['spielID' => 'id']],
            [['spielerID', 'spieler2ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spielerID' => 'id']],
        ];
    }
    
    public function getSpieler()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spielerID']);
    }
    
    public function getSpieler2()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler2ID']);
    }
    
    public function getSpiel()
    {
        return $this->hasOne(Spiel::class, ['id' => 'spielID']);
    }
    
}
?>