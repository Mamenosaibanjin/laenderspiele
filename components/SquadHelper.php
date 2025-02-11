<?php
namespace app\components;

use yii\helpers\Html;

class SquadHelper
{
    /**
     * Filtert und sortiert Spieler nach Position.
     *
     * @param array $squad - Liste aller Spieler.
     * @param int $positionID - Die ID der Position.
     * @return array - Gefilterte und sortierte Spieler.
     */
    public static function getFilteredAndSortedPlayers(array $squad, int $positionID): array
    {
        // Spieler filtern & nur relevante Position beibehalten
        $filteredSquad = array_filter($squad, function ($spieler) use ($positionID) {
            // Prüfen, ob landWettbewerb existiert und ein Array ist
            if (!isset($spieler->_related['landWettbewerb']) || !is_array($spieler->_related['landWettbewerb'])) {
                return false;
            }
            
            // Überprüfen, ob mindestens eine Position übereinstimmt
            foreach ($spieler->_related['landWettbewerb'] as $wettbewerb) {
                if (isset($wettbewerb->positionID) && $wettbewerb->positionID === $positionID) {
                    return true;
                }
            }
            
            return false;
        });
        
            echo "<pre>";var_dump($filteredSquad);echo "</pre>";
            // Spieler sortieren
            usort($filteredSquad, fn($a, $b) => strcmp($a->name, $b->name));
            
            return $filteredSquad;
    }
    
    
    /** Funktion zur Anzeige eines Kaders
     * 
     * @param array $players
     * @param array $positionMapping
     * @param string $title
     * @param string $linkUrl
     * @param string $linkText
     */
    public static function renderSquad($players, $positionMapping, $title, $linkUrl, $linkText) {
        if (empty($players)) {
           return;
        }
        
        echo "<h4>{$title}</h4><br>";
        echo '<div class="row five-column-layout">';
        
        foreach ($positionMapping as $positionID => $positionName) {
            echo '<div class="col-5">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="title">' . Html::encode($positionName) . '</h4>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled">';
            
            foreach ($players as $player) {
                // Prüfen, ob die Position zur aktuellen $positionID passt
                if (!isset($player->landWettbewerb) || !is_array($player->landWettbewerb)) {
                    continue;
                } 
                
                $hasMatchingPosition = false;
                foreach ($player->landWettbewerb as $wettbewerb) {
                    if (isset($wettbewerb->positionID) && $wettbewerb->positionID === $positionID) {
                        $hasMatchingPosition = true;
                        break;
                    }
                }
                
                if (!$hasMatchingPosition) {
                    continue;
                }
                
                // Spieler anzeigen
                echo '<li>';
                if (!empty($player->nati1)) {
                    echo Helper::renderFlag($player->nati1) . ' ';
                }
                echo Html::a(Html::encode($player->name . ($player->vorname ? ', ' . mb_substr($player->vorname, 0, 1, 'UTF-8') . '.' : '')), ['/spieler/view', 'id' => $player->id], ['class' => 'text-decoration-none']);
                echo '</li>';
            }
            
            echo '        </ul>
                </div>
            </div>
        </div>';
        }
        
        echo '<div style="text-align: right;">';
        echo Html::a(Html::encode($linkText), $linkUrl, ['class' => 'text-decoration-none']);
        echo '</div>';
        echo '</div>';
    }
}
?>