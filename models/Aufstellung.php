<?php 
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Aufstellung extends ActiveRecord
{
    public static function tableName()
    {
        return 'aufstellung';
    }

    public function rules()
    {
        return [
            [['spieler1ID', 'spieler2ID', 'spieler3ID', 'spieler4ID', 'spieler5ID', 'spieler6ID', 'spieler7ID', 'spieler8ID', 'spieler9ID', 'spieler10ID', 'spieler11ID', 'coachID'], 'integer'],
        
            // Existenzprüfung für alle Spieler-IDs und Coach
            [['spieler1ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler1ID' => 'id']],
            [['spieler2ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler2ID' => 'id']],
            [['spieler3ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler3ID' => 'id']],
            [['spieler4ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler4ID' => 'id']],
            [['spieler5ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler5ID' => 'id']],
            [['spieler6ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler6ID' => 'id']],
            [['spieler7ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler7ID' => 'id']],
            [['spieler8ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler8ID' => 'id']],
            [['spieler9ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler9ID' => 'id']],
            [['spieler10ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler10ID' => 'id']],
            [['spieler11ID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['spieler11ID' => 'id']],
            [['coachID'], 'exist', 'skipOnError' => true, 'targetClass' => Spieler::class, 'targetAttribute' => ['coachID' => 'id']],
        ];

    }

    public function getSpieler1()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler1ID']);
    }

    public function getSpieler2()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler2ID']);
    }

    public function getSpieler3()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler3ID']);
    }

    public function getSpieler4()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler4ID']);
    }

    public function getSpieler5()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler5ID']);
    }

    public function getSpieler6()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler6ID']);
    }

    public function getSpieler7()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler7ID']);
    }

    public function getSpieler8()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler8ID']);
    }

    public function getSpieler9()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler9ID']);
    }

    public function getSpieler10()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler10ID']);
    }

    public function getSpieler11()
    {
        return $this->hasOne(Spieler::class, ['id' => 'spieler11ID']);
    }

    public function getCoach()
    {
        return $this->hasOne(Spieler::class, ['id' => 'coachID']);
    }
}
?>