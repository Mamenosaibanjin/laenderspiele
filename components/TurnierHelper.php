<?php
namespace app\components;

use app\models\Club;
use app\models\Spiel;
use Yii;

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
        ->select('clubs.id')
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
}

?>