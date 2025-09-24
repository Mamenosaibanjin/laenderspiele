<?php
namespace app\components;

use app\models\Turnier;
use app\models\Gruppenmarkierung;
use Yii;
use yii\helpers\ArrayHelper;

class TabellenHelper
{

    // in components/TabellenHelper.php (ersetzt die alte berechneTabelle-Funktion)
    public static function berechneTabelle($turnierID, $rundeID, $spieltagMax = 1)
    {
        // 1) Spiele laden
        $spiele = Turnier::find()->joinWith([
            'spiel s',
            'spiel.club1 c1',
            'spiel.club2 c2'
        ])
        ->where(['rundeID' => $rundeID])
        ->andWhere(['tournamentID' => $turnierID])
        ->andWhere(['<=', 'spieltag', $spieltagMax])
        ->andWhere(['and',
            ['not', ['tore1' => null]],
            ['not', ['tore2' => null]],
        ])
        ->all();
        
        // 2) Clubs Grunddaten aufbauen
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
                $toreEigen = (int)($istHeim ? $spiel->spiel->tore1 : $spiel->spiel->tore2);
                $toreGegner = (int)($istHeim ? $spiel->spiel->tore2 : $spiel->spiel->tore1);
                
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
        
        // 3) Tiebreak-Regeln laden
        $rules = \app\models\TiebreakRule::find()
        ->where(['tournament_id' => $turnierID])
        ->orderBy(['priority' => SORT_ASC])
        ->with('tiebreakType')
        ->all();
        
        foreach ($rules as $rule) {
            Yii::info("Geladene Regel: id={$rule->id}, type={$rule->tiebreakType->code}", 'tabelle');
        }
        
        // 4) Clubs nach Punkten gruppieren
        $punkteGruppen = [];
        foreach ($clubs as $clubID => $data) {
            $punkteGruppen[$data['punkte']][] = $clubID;
        }
        krsort($punkteGruppen, SORT_NUMERIC);
        
        $finalOrder = [];
        
        foreach ($punkteGruppen as $punkte => $clubIDs) {
            if (count($clubIDs) === 1) {
                $finalOrder[] = $clubIDs[0];
                continue;
            }
            
            Yii::info("Tiebreak Gruppe (Punkte={$punkte}): " . implode(',', $clubIDs), 'tabelle');
            $miniStats = self::buildMiniStatsForGroup($clubIDs, $spiele);
            Yii::info("MiniStats: " . print_r($miniStats, true), 'tabelle');
            
            // Sortierung anhand Regeln
            usort($clubIDs, function($a, $b) use ($miniStats, $clubs, $rules) {
                foreach ($rules as $idx => $rule) {
                    Yii::info("Switch Type = {$rule->tiebreakType->code}", 'tabelle');
                    
                    switch ($rule->tiebreakType->code) {
                        case 'direct_points':
                            $pa = $miniStats[$a]['punkte'] ?? 0;
                            $pb = $miniStats[$b]['punkte'] ?? 0;
                            Yii::info("Vergleich [direct_points] {$clubs[$a]['club']->name}($pa) vs {$clubs[$b]['club']->name}($pb)", 'tabelle');
                            if ($pa !== $pb) {
                                $res = $pb - $pa;
                                Yii::info(" → Ergebnis direct_points = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'direct_goal_diff':
                            $da = ($miniStats[$a]['tore'] ?? 0) - ($miniStats[$a]['gegentore'] ?? 0);
                            $db = ($miniStats[$b]['tore'] ?? 0) - ($miniStats[$b]['gegentore'] ?? 0);
                            Yii::info("Vergleich [direct_goal_diff] {$clubs[$a]['club']->name}($da) vs {$clubs[$b]['club']->name}($db)", 'tabelle');
                            if ($da !== $db) {
                                $res = $db - $da;
                                Yii::info(" → Ergebnis direct_goal_diff = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'direct_goals':
                            $ga = $miniStats[$a]['tore'] ?? 0;
                            $gb = $miniStats[$b]['tore'] ?? 0;
                            Yii::info("Vergleich [direct_goals] {$clubs[$a]['club']->name}($ga) vs {$clubs[$b]['club']->name}($gb)", 'tabelle');
                            if ($ga !== $gb) {
                                $res = $gb - $ga;
                                Yii::info(" → Ergebnis direct_goals = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'overall_goal_diff':
                            $oda = $clubs[$a]['tore'] - $clubs[$a]['gegentore'];
                            $odb = $clubs[$b]['tore'] - $clubs[$b]['gegentore'];
                            Yii::info("Vergleich [overall_goal_diff] {$clubs[$a]['club']->name}($oda) vs {$clubs[$b]['club']->name}($odb)", 'tabelle');
                            if ($oda !== $odb) {
                                $res = $odb - $oda;
                                Yii::info(" → Ergebnis overall_goal_diff = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'overall_goals':
                            Yii::info("Vergleich [overall_goals] {$clubs[$a]['club']->name}({$clubs[$a]['tore']}) vs {$clubs[$b]['club']->name}({$clubs[$b]['tore']})", 'tabelle');
                            if ($clubs[$a]['tore'] !== $clubs[$b]['tore']) {
                                $res = $clubs[$b]['tore'] - $clubs[$a]['tore'];
                                Yii::info(" → Ergebnis overall_goals = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'fiarplay':
                            $fpa = self::getFairplayPoints($a);
                            $fpb = self::getFairplayPoints($b);
                            Yii::info("Vergleich [fairplay] {$clubs[$a]['club']->name}($fpa) vs {$clubs[$b]['club']->name}($fpb)", 'tabelle');
                            if ($fpa !== $fpb) {
                                $res = $fpa - $fpb;
                                Yii::info(" → Ergebnis fairplay = $res", 'tabelle');
                                return $res;
                            }
                            break;
                            
                        case 'drawing':
                            Yii::info("Vergleich [drawing] {$a} vs {$b}", 'tabelle');
                            return $a - $b;
                    }
                }
                
                Yii::info("Vergleich [fallback] {$a} vs {$b}", 'tabelle');
                return $a - $b;
            });
                
                Yii::info("Tiebreak sortiert: " . implode(',', array_map(fn($cid) => $clubs[$cid]['club']->name, $clubIDs)), 'tabelle');
                
                foreach ($clubIDs as $id) $finalOrder[] = $id;
        }
        
        // 5) Ergebnisarray aufbauen
        $result = [];
        foreach ($finalOrder as $cid) {
            $result[] = $clubs[$cid];
        }
        
        return $result;
    }
    
    /**
     * Mini-Tabelle nur für die Teams innerhalb einer Punkte-Gruppe.
     */
    private static function buildMiniStatsForGroup(array $clubIDs, array $spiele): array
    {
        $mini = [];
        foreach ($clubIDs as $cid) {
            $mini[$cid] = [
                'spiele' => 0,
                'siege' => 0,
                'remis' => 0,
                'niederlagen' => 0,
                'tore' => 0,
                'gegentore' => 0,
                'punkte' => 0,
            ];
        }
        
        foreach ($spiele as $spiel) {
            $home = $spiel->spiel->club1ID;
            $away = $spiel->spiel->club2ID;
            if (!in_array($home, $clubIDs, true) || !in_array($away, $clubIDs, true)) {
                continue;
            }
            
            $t1 = (int)$spiel->spiel->tore1;
            $t2 = (int)$spiel->spiel->tore2;
            
            $mini[$home]['spiele']++;
            $mini[$home]['tore'] += $t1;
            $mini[$home]['gegentore'] += $t2;
            
            $mini[$away]['spiele']++;
            $mini[$away]['tore'] += $t2;
            $mini[$away]['gegentore'] += $t1;
            
            if ($t1 > $t2) {
                $mini[$home]['siege']++; $mini[$home]['punkte'] += 3;
                $mini[$away]['niederlagen']++;
            } elseif ($t1 === $t2) {
                $mini[$home]['remis']++; $mini[$home]['punkte']++;
                $mini[$away]['remis']++; $mini[$away]['punkte']++;
            } else {
                $mini[$away]['siege']++; $mini[$away]['punkte'] += 3;
                $mini[$home]['niederlagen']++;
            }
        }
        
        return $mini;
    }
    
    
    /**
     * Placeholder: compute fairplay points for a club across tournament (neglect for now or implement using games table)
     * Return lower is better (fewer penalty points).
     */
    private static function getFairplayPoints($clubID)
    {
        // TODO: implementiere Abfrage an games/tabelle für gelbe/rote Karten, etc.
        // Für jetzt: 0 = keine Strafe (alle gleich)
        return 0;
    }
    
    public static function getPlatzfarben($turnierID, $rundeID)
    {
        $farben = [];
        $markierungen = Gruppenmarkierung::find()->where([
            'tournamentID' => $turnierID,
            'rundeID' => $rundeID
        ])->all();

        foreach ($markierungen as $m) {
            for ($i = $m->platz_ab; $i <= $m->platz_bis; $i ++) {
                $farben[$i] = $m->farbe;
            }
        }
        return $farben;
    }

    private static function compareDirectPoints($a, $b, $spiele)
    {
        $pointsA = 0;
        $pointsB = 0;
        foreach ($spiele as $spiel) {
            if (in_array($a['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ]) && in_array($b['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ])) {

                $toreA = $spiel->spiel->club1ID === $a['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;
                $toreB = $spiel->spiel->club1ID === $b['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;

                if ($toreA > $toreB)
                    $pointsA += 3;
                elseif ($toreA < $toreB)
                    $pointsB += 3;
                else {
                    $pointsA ++;
                    $pointsB ++;
                }
            }
        }
        return $pointsB - $pointsA;
    }

    private static function compareDirectGoalDiff($a, $b, $spiele)
    {
        $diffA = 0;
        $diffB = 0;
        foreach ($spiele as $spiel) {
            if (in_array($a['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ]) && in_array($b['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ])) {

                $toreA = $spiel->spiel->club1ID === $a['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;
                $toreB = $spiel->spiel->club1ID === $b['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;

                $diffA += $toreA - $toreB;
                $diffB += $toreB - $toreA;
            }
        }
        return $diffB - $diffA;
    }

    private static function compareDirectGoals($a, $b, $spiele)
    {
        $goalsA = 0;
        $goalsB = 0;
        foreach ($spiele as $spiel) {
            if (in_array($a['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ]) && in_array($b['club']->id, [
                $spiel->spiel->club1ID,
                $spiel->spiel->club2ID
            ])) {

                $goalsA += $spiel->spiel->club1ID === $a['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;
                $goalsB += $spiel->spiel->club1ID === $b['club']->id ? $spiel->spiel->tore1 : $spiel->spiel->tore2;
            }
        }
        return $goalsB - $goalsA;
    }
}
