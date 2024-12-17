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
    
    public static function findTurniere($wettbewerbID, $jahr, $gruppe = null, $runde = null, $spieltag = null)
    {
        $query = self::find()
        ->where(['wettbewerbID' => $wettbewerbID, 'jahr' => $jahr])
        ->andFilterWhere(['gruppe' => $gruppe])
        ->andFilterWhere(['runde' => $runde])
        ->andFilterWhere(['spieltag' => $spieltag])
        ->orderBy(['datum' => SORT_ASC, 'zeit' => SORT_ASC]);
        
        return $query->all();
    }
    
    public static function findTeilnehmer($wettbewerbID, $jahr)
    {
        $subQuery1 = (new \yii\db\Query())
        ->select(['c.id', 'c.name', 'c.land'])
        ->from('turnier t')
        ->innerJoin('spiele s', 't.spielID = s.ID')
        ->innerJoin('clubs c', 's.club1ID = c.ID')
        ->where(['t.wettbewerbID' => $wettbewerbID, 't.jahr' => $jahr]);
        
        $subQuery2 = (new \yii\db\Query())
        ->select(['c.id', 'c.name', 'c.land'])
        ->from('turnier t')
        ->innerJoin('spiele s', 't.spielID = s.ID')
        ->innerJoin('clubs c', 's.club2ID = c.ID')
        ->where(['t.wettbewerbID' => $wettbewerbID, 't.jahr' => $jahr]);
        
        $query = (new \yii\db\Query())
        ->from(['unionQuery' => $subQuery1->union($subQuery2)])
        ->orderBy(['name' => SORT_ASC]);
        
        return $query->all();
    }
    
    public static function countSpieler($wettbewerbID, $jahr, $clubID)
    {
        return (new \yii\db\Query())
        ->from('spieler_land_wettbewerb slw')
        ->where([
            'slw.wettbewerbID' => $wettbewerbID,
            'slw.jahr' => $jahr,
            'slw.landID' => $clubID,
        ])
        ->count();
    }
    
    public function getErgebnis()
    {
        if ($this->spiel) {
            return $this->spiel->tore1 . ' : ' . $this->spiel->tore2;
        }
        return '- : -';
    }
    
    public function getErgebnisHtml()
    {
        if ($this->spiel) {
            return "<div class='digital-scoreboard'>
                    <span class='digit'>{$this->spiel->tore1}</span>
                    <span class='divider'>:</span>
                    <span class='digit'>{$this->spiel->tore2}</span>
                </div>";
        }
        return "<div class='digital-scoreboard'>
                <span class='digit'>-</span>
                <span class='divider'>:</span>
                <span class='digit'>-</span>
            </div>";
    }
    
    public function getFormattedDate()
    {
        return \Yii::$app->formatter->asDate($this->datum, 'php:d.m.Y');
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
    
    public function getClub1()
    {
        return $this->hasOne(Club::class, ['id' => 'club1ID'])
        ->via('spiel');
    }
    
    public function getClub2()
    {
        return $this->hasOne(Club::class, ['id' => 'club2ID'])
        ->via('spiel');
    }
}
?>