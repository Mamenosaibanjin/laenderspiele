<?php

namespace app\components;

use app\models\Turnier;
use app\models\Gruppenmarkierung;
use yii\helpers\ArrayHelper;

class TabellenHelper
{
    public static function berechneTabelle($turnierID, $rundeID, $spieltagMax = 1)
    {
        // Turnier laden
        $tournament = \app\models\Tournament::findOne($turnierID);
        if (!$tournament) {
            throw new \yii\web\NotFoundHttpException("Turnier nicht gefunden.");
        }
        
        $spiele = Turnier::find()
        ->joinWith('spiel')
        ->where(['rundeID' => $rundeID])
        ->andWhere(['tournamentID' => $turnierID])
        ->andWhere(['<=', 'spieltag', $spieltagMax])
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
        
        // === Sortierlogik ===
        usort($clubs, function ($a, $b) use ($spiele, $tournament) {
            // 1. Punkte
            if ($a['punkte'] !== $b['punkte']) {
                return $b['punkte'] - $a['punkte'];
            }
            
            if ($tournament->differenceFirst) {
                // 2a. Tordifferenz
                $diffA = $a['tore'] - $a['gegentore'];
                $diffB = $b['tore'] - $b['gegentore'];
                if ($diffA !== $diffB) return $diffB - $diffA;
                
                // 3a. Tore
                return $b['tore'] - $a['tore'];
            } else {
                // 2b. Direkter Vergleich
                $clubA = $a['club']->id;
                $clubB = $b['club']->id;
                
                $punkteA = 0;
                $punkteB = 0;
                $toreA = 0;
                $toreB = 0;
                
                foreach ($spiele as $spiel) {
                    if (
                        ($spiel->spiel->club1ID == $clubA && $spiel->spiel->club2ID == $clubB) ||
                        ($spiel->spiel->club1ID == $clubB && $spiel->spiel->club2ID == $clubA)
                        ) {
                            $t1 = $spiel->spiel->tore1;
                            $t2 = $spiel->spiel->tore2;
                            
                            if ($spiel->spiel->club1ID == $clubA) {
                                $toreA += $t1;
                                $toreB += $t2;
                                if ($t1 > $t2) $punkteA += 3;
                                elseif ($t1 == $t2) {
                                    $punkteA += 1;
                                    $punkteB += 1;
                                } else $punkteB += 3;
                            } else {
                                $toreA += $t2;
                                $toreB += $t1;
                                if ($t2 > $t1) $punkteA += 3;
                                elseif ($t2 == $t1) {
                                    $punkteA += 1;
                                    $punkteB += 1;
                                } else $punkteB += 3;
                            }
                        }
                }
                
                if ($punkteA !== $punkteB) return $punkteB - $punkteA;
                $diffA = $toreA - $toreB;
                $diffB = $toreB - $toreA;
                if ($diffA !== $diffB) return $diffB - $diffA;
                
                // Falls auch direkter Vergleich gleich → Tore
                return $b['tore'] - $a['tore'];
            }
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
