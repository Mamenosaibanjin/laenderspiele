<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flags".
 *
 * @property int $id
 * @property string $key
 * @property string $name_de
 * @property string $name_en
 * @property string $name_fr
 * @property string|null $startdatum
 * @property string|null $enddatum
 * @property string $flag_url
 */
class Flags extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flags';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'name_de', 'name_en', 'name_fr', 'flag_url'], 'required'],
            [['startdatum', 'enddatum'], 'date', 'format' => 'php:Y-m-d'],
            [['key'], 'string', 'max' => 10],
            [['name_de', 'name_en', 'name_fr'], 'string', 'max' => 255],
            [['flag_url'], 'string', 'max' => 500],
            [['key', 'startdatum', 'enddatum'], 'unique', 'targetAttribute' => ['key', 'startdatum', 'enddatum'], 'message' => 'Die Kombination aus Key, Startdatum und Enddatum muss einzigartig sein.'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Länderkürzel',
            'name_de' => 'Name (Deutsch)',
            'name_en' => 'Name (Englisch)',
            'name_fr' => 'Name (Französisch)',
            'startdatum' => 'Startdatum',
            'enddatum' => 'Enddatum',
            'flag_url' => 'Flaggen-URL',
        ];
    }
}
