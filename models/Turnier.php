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
            [['tournamentID', 'spielID', 'spieltag'], 'integer'],
            [['datum'], 'date', 'format' => 'php:Y-m-d'],
            [['zeit'], 'time', 'format' => 'php:H:i'],
            [['gruppe'], 'string', 'max' => 15],
            [['beschriftung'], 'string', 'max' => 255],
            [['aktiv', 'tore'], 'boolean', 'trueValue' => 1, 'falseValue' => 0],
            [['jahr', 'wettbewerbID'], 'safe'],
            
            // Neue Referenzprüfung
            [['tournamentID'], 'exist', 'targetClass' => \app\models\Tournament::class, 'targetAttribute' => 'ID'],
            [['spielID'], 'exist', 'targetClass' => \app\models\Spiel::class, 'targetAttribute' => 'id'],
        ];
    }
    
    
    public static function findTurniere($tournamentID, $gruppe = null, $spieltag = null)
    {
        $query = self::find()
        ->alias('s')
        ->select([
            's.*',                  // alle Spalten aus der turnier-Tabelle
            't.wettbewerbID',
            't.jahr'
        ])
        ->innerJoin(['t' => 'tournament'], 's.tournamentID = t.ID')
        ->where(['s.tournamentID' => $tournamentID])
        ->andFilterWhere(['s.gruppe' => $gruppe])
        ->andFilterWhere(['s.spieltag' => $spieltag])
        ->orderBy(['s.datum' => SORT_ASC, 's.zeit' => SORT_ASC]);
        
        return $query->asArray()->all(); // wichtig: asArray(), damit zusätzliche Felder direkt im Array landen
    }
    
    
    public static function findTeilnehmer($tournamentID)
    {
        $subQuery1 = (new \yii\db\Query())
        ->select(['c.id', 'c.name', 'c.land'])
        ->from('turnier t')
        ->innerJoin('spiele s', 't.spielID = s.ID')
        ->innerJoin('clubs c', 's.club1ID = c.ID')
        ->where(['t.tournamentID' => $tournamentID]);
        
        $subQuery2 = (new \yii\db\Query())
        ->select(['c.id', 'c.name', 'c.land'])
        ->from('turnier t')
        ->innerJoin('spiele s', 't.spielID = s.ID')
        ->innerJoin('clubs c', 's.club2ID = c.ID')
        ->where(['t.tournamentID' => $tournamentID]);
        
        $query = (new \yii\db\Query())
        ->from(['unionQuery' => $subQuery1->union($subQuery2)])
        ->orderBy(['name' => SORT_ASC]);
        
        return $query->all();
    }
    
    public static function countSpieler($tournamentID, $clubID)
    {
        return (new \yii\db\Query())
        ->from('spieler_land_wettbewerb slw')
        ->leftJoin('tournament t', 't.id = slw.tournamentID')
        ->where([
            'OR',
            ['slw.tournamentID' => $tournamentID], // Falls noch alte Einträge existieren
            ['t.id' => $tournamentID]      // Neue Struktur über tournament-Tabelle
        ])
        ->andWhere(['slw.landID' => $clubID])
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
    
    public static function countTore($tournamentID): int
    {
        return (new \yii\db\Query())
        ->from('games g')
        ->innerJoin('turnier t', 'g.spielID = t.spielID')
        ->where([
            't.tournamentID' => $tournamentID,
        ])
        ->andWhere(['g.aktion' => ['ET', 'TOR', '11m']])
        ->count();
    }
    
    public static function countPlatzverweise($tournamentID): int
    {
        return (new \yii\db\Query())
        ->from('games g')
        ->innerJoin('turnier t', 'g.spielID = t.spielID')
        ->where([
            't.tournamentID' => $tournamentID,
        ])
        ->andWhere(['g.aktion' => ['GRK', 'RK']])
        ->count();
    }
    
    public function getTopScorers($tournamentID, $limit = 20)
    {
        return Spieler::find()
        ->select([
            'spieler.nati1',
            'spieler.id',
            'spieler.vorname', 
            'spieler.name',
            'COUNT(CASE WHEN games.aktion LIKE "TOR" OR games.aktion LIKE "11m" THEN 1 END) AS tor'
        ])
        ->joinWith(['games', 'games.spiel.turnier'])
        ->where([
            'turnier.tournamentID' => $tournamentID
        ])
        ->andWhere(['or', ['like', 'games.aktion', 'TOR'], ['like', 'games.aktion', '11m']])
        ->groupBy('spieler.id')
        ->orderBy(['tor' => SORT_DESC, 'spieler.name' => SORT_ASC])
        ->limit($limit)
        ->asArray()
        ->all();
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
    
    public function getRunde()
    {
        return $this->hasOne(Runde::class, ['id' => 'rundeID']);
    }
    
}
?>