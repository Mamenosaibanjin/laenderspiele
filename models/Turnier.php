<?php
namespace app\models;

use yii\db\ActiveRecord;

class Turnier extends ActiveRecord
{
    /**
     * Gibt den Namen der Tabelle zurück.
     */
    public static function tableName()
    {
        return 'turnier';
    }
    
    public function rules()
    {
        return [
            [['wettbewerbID', 'spielID', 'spieltag', 'runde'], 'integer'], // Zahlenwerte
            [['jahr', 'datum'], 'date', 'format' => 'php:Y-m-d'], // Datumswerte
            [['zeit'], 'time', 'format' => 'php:H:i:s'], // Zeit
            [['gruppe'], 'string', 'max' => 15], // Kürzere Texte
            [['beschriftung'], 'string', 'max' => 255], // Beschriftung
            [['aktiv', 'tore'], 'boolean'], // Booleans
            [['wettbewerbID'], 'exist', 'targetClass' => Wettbewerb::class, 'targetAttribute' => 'id'], // Prüfung auf Wettbewerb
            [['spielID'], 'exist', 'targetClass' => Spiel::class, 'targetAttribute' => 'id'], // Prüfung auf Spiel
        ];
    }
    
    /**
     * Relationen zu anderen Tabellen.
     */
    
    public function getWettbewerb()
    {
        return $this->hasOne(Wettbewerb::class, ['id' => 'wettbewerbID']);
    }
    
    public function getSpiel()
    {
        return $this->hasOne(Spiel::class, ['id' => 'spielID']);
    }
}
?>