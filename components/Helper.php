<?php

namespace app\components;

use app\models\Nation;

class Helper
{

    
    public static function getFlagUrl($iocCode, $date = null)
    {
        // Abfrage der Nation anhand des IOC-Codes
        $nation = Nation::findOne(['kuerzel' => $iocCode]);
        if (!$nation) {
            return null; // Kein ISO-Code gefunden, keine Flagge verfügbar
        }
        
        $isoCode = strtolower($nation->ISO3166);
        $baseUrl = "https://flagpedia.net/data/flags/w580/";
        $currentFlag = $isoCode . ".png";
        
        // Datumskonvertierung
        $dateTimestamp = null;
        if ($date !== null) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) { // Format: YYYY-MM-DD
                $dateTimestamp = strtotime($date);
            } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) { // Format: DD.MM.YYYY
                $dateTimestamp = strtotime(str_replace('.', '-', $date));
            } elseif (preg_match('/^\d{6}$/', $date)) { // Format: YYYYMM
                $dateTimestamp = strtotime(substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-01');
            }
        }
        
        // Historische Flaggen-Logik
        $historicalFlags = [
            'IRQ' => [
                ['start' => '01.01.1991', 'end' => '30.06.2004', 'url' => 'https://example.com/flags/irq_1991_2004.png'],
            ],
            'ba' => [
                ['start' => '01.03.1992', 'end' => '04.02.1998', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Flag_of_Bosnia_and_Herzegovina_%281992%E2%80%931998%29.svg/1920px-Flag_of_Bosnia_and_Herzegovina_%281992%E2%80%931998%29.svg.png'],
            ],
        ];
        if ($dateTimestamp !== null && isset($historicalFlags[$isoCode])) {
            foreach ($historicalFlags[$isoCode] as $flag) {
                $startTimestamp = strtotime(str_replace('.', '-', $flag['start']));
                $endTimestamp = strtotime(str_replace('.', '-', $flag['end']));
                if ($dateTimestamp >= $startTimestamp && $dateTimestamp <= $endTimestamp) {
                    return $flag['url'];
                }
            }
        }
        
        // Aktuelle Flagge zurückgeben, wenn keine historische Flagge zutrifft
        return $baseUrl . $currentFlag;
    }
    /**
     * Gibt die URL eines Vereinswappens zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Die URL des Vereinswappens.
     */
    public static function getClubLogoUrl($clubId)
    {
        $basePath = \Yii::getAlias('@webroot/assets/img/vereine/');
        $baseUrl = \Yii::getAlias('@web/assets/img/vereine/');
        
        // Dateiname basierend auf der Club-ID
        $filePath = $basePath . $clubId . '.gif';
        
        // Prüfe, ob die Datei existiert
        if (file_exists($filePath)) {
            return $baseUrl . $clubId . '.gif';
        }
        
        // Fallback: Standardbild, wenn kein Logo verfügbar
        return \Yii::getAlias('@web/assets/img/vereine/standard.gif');
    }

    /**
     * Gibt den Nationskürzel eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Nationskürzel des Vereinswappens.
     */
    public static function getClubNation($clubId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalte `land` aus der Club-Tabelle abzurufen
        $sql = "SELECT land FROM clubs WHERE id = :clubId";
        
        // Führe die Abfrage aus und hole das Ergebnis
        $land = $db->createCommand($sql)
        ->bindValue(':clubId', $clubId)
        ->queryScalar();
        
        // Überprüfen, ob ein Ergebnis gefunden wurde
        if ($land) {
            return $land; // Gebe den Wert der Spalte `land` zurück
        }
    
    }
    /**
     * Gibt den Namen eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Name des Vereinswappens.
     */
    public static function getClubName($clubId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalte `land` aus der Club-Tabelle abzurufen
        $sql = "SELECT name FROM clubs WHERE id = :clubId";
        
        // Führe die Abfrage aus und hole das Ergebnis
        $name = $db->createCommand($sql)
        ->bindValue(':clubId', $clubId)
        ->queryScalar();
        
        // Überprüfen, ob ein Ergebnis gefunden wurde
        if ($name) {
            return $name; // Gebe den Wert der Spalte `land` zurück
        }
    }
    
    public static function colorToHex($colorName) {
   
        $colors = [
            'weiss' => '#FFFFFF',
            'schwarz' => '#000000',
            'rot' => '#FF0000',
            'blau' => '#0000FF',
            'gelb' => '#FFFF00',
            'grün' => '#008000',
            'violett' => '#441678',
            'himmelblau' => '#6CABDD',
            // Weitere Farben hier einfügen
        ];
        
        return $colors[strtolower($colorName)] ?? '#FFFFFF'; // Fallback zu Schwarz
    }
    
    public static function getImVereinSeit($player, $clubID, $year)
    {
        $vereinSaison = $player->vereinSaison;
        $latestYear = null; 
        
        // Das Saisonende berechnen
        $saisonEnde = intval($year+1 . '06');
        foreach ($vereinSaison as $entry) {
            if ($entry->vereinID == $clubID) {
                // Prüfen, ob der Zeitraum gültig ist
                $entryStart = intval($entry->von);
                $entryEnd = intval($entry->bis);
                
                if ($entryStart <= $saisonEnde && $entryEnd >= $year . '01') {
                    // Spätesten Transfer in der Saison auswählen
                    $entryYear = substr($entry->von, 0, 4); // Nur das Jahr des Transfers
                    if ($latestYear === null || $entryYear > $latestYear) {
                        $latestYear = $entryYear;
                    }
                }
            }
        }
        
        return $latestYear;
    }
    
    private static function getVorherigerVereinName($currentClubName, $vorherigerVerein)
    {
        $vorherigerClubName = $vorherigerVerein->verein->name;
        $jugend = $vorherigerVerein->jugend == 1;
        
        // Prüfen, ob der vorherige Verein ein Teil des aktuellen Vereinsnamens ist
        if (stripos($vorherigerClubName, $currentClubName) === 0) {
            // Sonderfall: Jugend
            if ($jugend) {
                return 'eigene Jugend';
            }
            
            // Prüfen auf Zusätze wie "II", "III", etc.
            $suffix = trim(str_ireplace($currentClubName, '', $vorherigerClubName));
            if (!empty($suffix)) {
                // Beispiel: "Hamburger SV II" -> "eigene Zweite"
                return 'eigene ' . $suffix;
            } else {
                // Beispiel: "Hamburger SV" -> "eigene Erste"
                return 'eigene Erste';
            }
        }
        // Standard: Rückgabe des normalen Vereinsnamens
        return $vorherigerClubName;
    }
    
    
    public static function getVorherigerClub($player, $clubID)
    {
        $vereinSaison = $player->vereinSaison;
        $currentEntry = null;
        
        foreach ($vereinSaison as $entry) {
            if ($entry->vereinID == $clubID) {
                $currentEntry = $entry;
                break;
            }
        }
        
        if (!$currentEntry) {
            return null;
        }
        
        // Vorgängerverein finden
        $vorherigeVereine = array_filter($vereinSaison, function ($entry) use ($currentEntry) {
            return $entry->bis < $currentEntry->von;
        });
            
            usort($vorherigeVereine, function ($a, $b) {
                return $b->bis <=> $a->bis; // Absteigend sortieren
            });
                
                $vorherigerVerein = reset($vorherigeVereine);
                
                if ($vorherigerVerein) {
                    return $vorherigerVerein->verein->name;
                }
                
                return null;
    }
    
                /* Sonderfälle behandeln
                if ($vorherigerVerein) {
                    if (stripos($vorherigerVerein->verein->name, $clubID) !== false) {
                        if ($vorherigerVerein->jugend == 1) {
                            return 'eigene Jugend';
                        }
                        return 'eigene ' . $vorherigerVerein->verein->name;
                    }
                    
                    if ($vorherigerVerein->jugend == 1) {
                        $jugend = ' Jugend';
                    } else {
                        $jugend = '';
                    }
                
                    return $vorherigerVerein->verein->name;
                    
                }*/
    
    public static function getVorherigerClubID($player, $clubID, $jahr)
    {
        $vereinSaison = $player->vereinSaison;
        $currentEntry = null;
        
        foreach ($vereinSaison as $entry) {
            if ($entry->vereinID == $clubID) {
                $currentEntry = $entry;
                break;
            }
        }
        
        if (!$currentEntry) {
            return null;
        }
        
        // Vorgängerverein finden
        $vorherigeVereine = array_filter($vereinSaison, function ($entry) use ($currentEntry) {
            return $entry->bis < $currentEntry->von;
        });
            
            usort($vorherigeVereine, function ($a, $b) {
                return $b->bis <=> $a->bis; // Absteigend sortieren
            });
                
                $vorherigerVerein = reset($vorherigeVereine);
                
                if ($vorherigerVerein) {
                    return $vorherigerVerein->verein->id;
                }
                
                return null;
    }
     
    
    /**
     * Gibt die IDs der Vereine während eines Turniers zurück.
     */
    public static function getClubsAtTurnier($playerId, $turnier, $jahr)
    {
        // Wenn kein Turnier angegeben ist
        if ($turnier == 0) {
            $query = (new \yii\db\Query())
            ->select(['c.id'])
            ->from(['c' => 'clubs'])
            ->innerJoin(['svs' => 'spieler_verein_saison'], 'svs.vereinID = c.id')
            ->where([
                'svs.spielerID' => $playerId,
                'svs.jugend' => 0,
            ])
            ->andWhere([
                'or',
                ['and', ['>=', 'svs.bis', $jahr . '01'], ['<=', 'svs.von', $jahr . '12']],
                ['between', 'svs.bis', $jahr . '01', $jahr . '12'],
                ['between', 'svs.von', $jahr . '01', $jahr . '12'],
            ])
            ->groupBy(['c.id'])
            ->orderBy(['svs.von' => SORT_DESC]);
            
            // Alle Ergebnisse abrufen
            $clubIDs = $query->column(); // Gibt ein Array aller IDs zurück
            
            return !empty($clubIDs) ? $clubIDs : null;
        }
        
        return null; // Wenn $turnier nicht 0 ist
    }
    
    public static function getTurniername($turnier)
    {
        $query = (new \yii\db\Query())
        ->select(['name', 'land'])
        ->from(['wettbewerb'])
        ->where(['ID' => $turnier])
        ->scalar();
        
        return $query;
    }
}
?>