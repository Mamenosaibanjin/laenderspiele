<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Typ extends ActiveRecord
{
    public static function tableName()
    {
        return 'typ';
    }
    
    public function rules()
    {
        return [
            [['name_de', 'name_en'], 'required'],
            [['name_de', 'name_en'], 'string', 'max' => 255],
        ];
    }
    
    public function getTyp()
    {
        return $this->hasOne(Typ::class, ['id' => 'typId']);
    }
}
?>