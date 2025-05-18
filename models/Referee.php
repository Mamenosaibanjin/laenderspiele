<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Referee extends ActiveRecord
{
    public $spiele;
    public $gk_count;
    public $grk_count;
    public $rk_count;
    
    public static function tableName()
    {
        return 'referee';
    }
    
    public function rules()
    {
        return [
            [['name', 'fullname'], 'required'],
            [['vorname', 'geburtstag', 'geburtsort', 'geburtsland', 'nati1'], 'string', 'max' => 255],
            [['clubID'], 'integer'],
            [['clubID'], 'exist', 'skipOnError' => true, 'targetClass' => Club::class, 'targetAttribute' => ['clubID' => 'id']],
        ];
    }
    
    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'clubID']);
    }
}
?>