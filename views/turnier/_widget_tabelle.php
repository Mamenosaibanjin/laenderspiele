<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use app\components\TabellenHelper;

class TabellenWidget extends Widget
{
    public int $turnierID;
    public int $rundeID;
    public int $spielID;
    public int $spieltagMin = 1;
    
    public function run()
    {
        $daten = TabellenHelper::berechneTabelle($this->turnierID, $this->rundeID, $this->spielID, $this->spieltagMin);
        $farben = TabellenHelper::getPlatzfarben($this->turnierID, $this->rundeID);
        
        $html = '<table class="table table-bordered table-sm text-center">';
        $html .= '<thead class="table-light"><tr>';
        $html .= '<th>#</th><th style="text-align:left;">Mannschaft</th><th>Sp.</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th>Dif.</th><th>Pkt.</th>';
        $html .= '</tr></thead><tbody>';
        
        $platz = 1;
        foreach ($daten as $club) {
            $diff = $club['tore'] - $club['gegentore'];
            $farbe = $farben[$platz] ?? null;
            $style = $farbe ? "background-color: $farbe;" : '';
            
            $html .= "<tr style='{$style}'>";
            $html .= "<td>{$platz}</td>";
            $html .= "<td style='text-align:left;'>" . Html::a($club['club']->name, ['club/view', 'id' => $club['club']->id]) . "</td>";
            $html .= "<td>{$club['spiele']}</td>";
            $html .= "<td>{$club['siege']}</td>";
            $html .= "<td>{$club['remis']}</td>";
            $html .= "<td>{$club['niederlagen']}</td>";
            $html .= "<td>{$club['tore']}:{$club['gegentore']}</td>";
            $html .= "<td>{$diff}</td>";
            $html .= "<td>{$club['punkte']}</td>";
            $html .= "</tr>";
            
            $platz++;
        }
        
        $html .= '</tbody></table>';
        return $html;
    }
}
