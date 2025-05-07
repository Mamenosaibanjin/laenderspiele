<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "gruppenmarkierung".
 *
 * @property int $id
 * @property int $tournamentID
 * @property int $rundeID
 * @property int $platz_ab
 * @property int $platz_bis
 * @property string $beschriftung
 * @property string $farbe
 */
class Gruppenmarkierung extends ActiveRecord
{
    public static function tableName()
    {
        return 'gruppenmarkierung';
    }
    
    public function rules()
    {
        return [
            [['tournamentID', 'rundeID', 'platz_ab', 'platz_bis', 'farbe'], 'required'],
            [['tournamentID', 'rundeID', 'platz_ab', 'platz_bis'], 'integer'],
            [['beschriftung', 'farbe'], 'string', 'max' => 255],
        ];
    }
}
