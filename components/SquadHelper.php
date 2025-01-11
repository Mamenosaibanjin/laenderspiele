<?php
namespace app\components;

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
        // Spieler filtern
        $filteredPlayers = array_filter($squad, function ($player) use ($positionID) {
            return ($player->vereinSaison[0]->positionID ?? null) === $positionID;
        });
            
            // Spieler sortieren
            usort($filteredPlayers, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
                
                return $filteredPlayers;
    }
}
?>