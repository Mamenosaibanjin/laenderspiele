<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "runde".
 *
 * @property int $id
 * @property int $turnierID
 * @property string $name
 * @property string|null $gruppenname
 * @property string $typ
 * @property int|null $reihenfolge
 *
 * @property Turnier $turnier
 * @property Spiel[] $spiele
 */
class Runde extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'runde';
    }
    
    public function rules()
    {
        return [
            [['turnierID', 'name', 'typ'], 'required'],
            [['turnierID', 'reihenfolge'], 'integer'],
            [['name', 'gruppenname'], 'string', 'max' => 255],
            [['typ'], 'in', 'range' => ['gruppe', 'ko']],
            [['turnierID'], 'exist', 'skipOnError' => true, 'targetClass' => Turnier::class, 'targetAttribute' => ['turnierID' => 'id']],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'turnierID' => 'Turnier',
            'name' => 'Name',
            'gruppenname' => 'Gruppenname',
            'typ' => 'Typ',
            'reihenfolge' => 'Reihenfolge',
        ];
    }
    
    public function getTurnier()
    {
        return $this->hasOne(Turnier::class, ['id' => 'turnierID']);
    }
    
    public function getSpiele()
    {
        return $this->hasMany(Spiel::class, ['rundeID' => 'id']);
    }
    
    public function getBezeichnung()
    {
        return $this->name . ($this->gruppenname ? ' – ' . $this->gruppenname : '');
    }
}
