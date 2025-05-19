<?php
namespace app\components;

use app\models\Club;
use app\models\Spiel;
use app\models\Turnier;
use Yii;
use yii\db\Query;

class TurnierHelper
{
    public static function getSieger($tournamentID, $rundeID = 8)
    {
        $gewinner = Club::find()
        ->innerJoin('spiele s', <<<SQL
            (s.Tore1 > s.Tore2 AND s.club1ID = clubs.id) OR
            (s.Tore2 > s.Tore1 AND s.club2ID = clubs.id)
        SQL)
        ->innerJoin('turnier t', 't.spielID = s.id')
        ->where([
            't.tournamentID' => $tournamentID,
            't.rundeID' => $rundeID,
        ])
        ->select(['clubs.id', 'clubs.land'])
        ->distinct()
        ->one();
        
        return $gewinner;
    }

    public static function getFinale($tournamentID, $rundeID = 8)
    {
        $finale = Spiel::find()
        ->innerJoin('turnier t', 't.spielID = spiele.id')
        ->where([
            't.tournamentID' => $tournamentID,
            't.rundeID' => $rundeID,
        ])
        ->one();
        
        return $finale;
    }
    
    public static function getRekordsieger($tournamentID)
    {
        $turniere = Turnier::findAlleTurniere($tournamentID, true); // nur beendete Turniere
        $siegerListe = [];
        
        foreach ($turniere as $turnier) {
            $sieger = self::getSieger($turnier['id']);
            if ($sieger) {
                $clubID = $sieger->id;
                if (!isset($siegerListe[$clubID])) {
                    $siegerListe[$clubID] = [
                        'clubID' => $clubID,
                        'land' => $sieger->land,
                        'siege' => 1,
                    ];
                } else {
                    $siegerListe[$clubID]['siege']++;
                }
            }
        }
        
        // Sortieren nach Anzahl Siege DESC
        usort($siegerListe, fn($a, $b) => $b['siege'] <=> $a['siege']);
        
        return $siegerListe;
    }
    
    public static function getTorschuetzenkoenig($tournamentID)
    {
        // Subquery: Ermittelt die höchste Anzahl Tore eines Spielers im Turnier
        $subQuery = (new Query())
        ->select(['MAX(tore_count)'])
        ->from([
            'tore_counts' => (new Query())
            ->select(['spielerID', 'COUNT(*) AS tore_count'])
            ->from('games g')
            ->innerJoin('turnier t', 't.spielID = g.spielID')
            ->where([
                't.tournamentID' => $tournamentID,
            ])
            ->andWhere(['IN', 'g.aktion', ['TOR', '11m']])
            ->andWhere(['<', 'g.minute', 200])
            ->groupBy('g.spielerID')
        ]);
        
        // Hauptquery: Holt alle Spieler mit genau dieser Toranzahl
        $query = (new Query())
        ->select(['g.spielerID', 'COUNT(*) AS tore'])
        ->from('games g')
        ->innerJoin('turnier t', 't.spielID = g.spielID')
        ->where([
            't.tournamentID' => $tournamentID,
        ])
        ->andWhere(['IN', 'g.aktion', ['TOR', '11m']])
        ->andWhere(['<', 'g.minute', 200])
        ->groupBy('g.spielerID')
        ->having(['COUNT(*)' => $subQuery]);
        
        return $query->all();
    }
}

?>