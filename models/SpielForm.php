<?php
use yii\base\Model;

class SpielForm extends Model
{
    public $datum;
    public $zeit;
    public $club1ID;
    public $club2ID;
    public $runde;
    
    public function rules()
    {
        return [
            [['datum', 'zeit', 'club1ID', 'club2ID', 'runde'], 'required'],
            [['datum'], 'date', 'format' => 'php:Y-m-d'],
            [['zeit'], 'time', 'format' => 'php:H:i'],
            [['club1ID', 'club2ID'], 'integer'],
            [['runde'], 'string'],
        ];
    }
}
