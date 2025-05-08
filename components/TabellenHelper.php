<?php

namespace app\components;

use app\models\Turnier;
use app\models\Gruppenmarkierung;
use yii\helpers\ArrayHelper;

class TabellenHelper
{
    public static function berechneTabelle($turnierID, $rundeID, $spieltagMax = 1)
    {
        $spiele = Turnier::find()
        ->joinWith('spiel') // falls Relation singular heißt
        ->where(['rundeID' => $rundeID])
        ->andWhere(['<=', 'spieltag', 2])
        ->andWhere(['and',
            ['not', ['tore1' => null]],
            ['not', ['tore2' => null]],
        ])
        ->all();
        $clubs = [];

        foreach ($spiele as $spiel) {

            foreach ([$spiel->spiel->club1ID, $spiel->spiel->club2ID] as $clubID) {
                if (!isset($clubs[$clubID])) {
                    $clubs[$clubID] = [
                        'club' => $spiel->spiel->club1ID === $clubID ? $spiel->spiel->club1 : $spiel->spiel->club2,
                        'spiele' => 0,
                        'siege' => 0,
                        'remis' => 0,
                        'niederlagen' => 0,
                        'tore' => 0,
                        'gegentore' => 0,
                        'punkte' => 0,
                    ];
                }
                
                $istHeim = $spiel->spiel->club1ID === $clubID;
                $toreEigen = $istHeim ? $spiel->spiel->tore1 : $spiel->spiel->tore2;
                $toreGegner = $istHeim ? $spiel->spiel->tore2 : $spiel->spiel->tore1;
                
                $clubs[$clubID]['spiele']++;
                $clubs[$clubID]['tore'] += $toreEigen;
                $clubs[$clubID]['gegentore'] += $toreGegner;
                
                if ($toreEigen > $toreGegner) {
                    $clubs[$clubID]['siege']++;
                    $clubs[$clubID]['punkte'] += 3;
                } elseif ($toreEigen == $toreGegner) {
                    $clubs[$clubID]['remis']++;
                    $clubs[$clubID]['punkte'] += 1;
                } else {
                    $clubs[$clubID]['niederlagen']++;
                }
            }
        }
        
        // Sortieren nach Punkten, Tordifferenz, Tore
        usort($clubs, function($a, $b) {
            if ($a['punkte'] !== $b['punkte']) return $b['punkte'] - $a['punkte'];
            $diffA = $a['tore'] - $a['gegentore'];
            $diffB = $b['tore'] - $b['gegentore'];
            if ($diffA !== $diffB) return $diffB - $diffA;
            return $b['tore'] - $a['tore'];
        });
            return $clubs;
    }
    
    public static function getPlatzfarben($turnierID, $rundeID)
    {
        $farben = [];
        $markierungen = Gruppenmarkierung::find()
        ->where(['tournamentID' => $turnierID, 'rundeID' => $rundeID])
        ->all();
        
        foreach ($markierungen as $m) {
            for ($i = $m->platz_ab; $i <= $m->platz_bis; $i++) {
                $farben[$i] = $m->farbe;
            }
        }
        
        return $farben;
    }
}
