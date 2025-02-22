<?php

namespace app\components;

use app\models\Nation;
use app\models\Flags;
use app\models\Typ;
use DateTime;
use Yii;
use yii\bootstrap5\Html;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Helper
{

    
    /**
     * Gibt die URL und den Namen zur Flagge zurück.
     * Ersetzt langfristig die bisherige Funktion getFlagUrl
     *      
     * @param string $iocCode Der Ländercode (IOC Code).
     * @return string|null Die URL der Flagge oder null, wenn keine verfügbar ist.
     */
    public static function getFlagInfo($key, $date = null)
    {
        $language = Yii::$app->language;
        $column = match ($language) {
            'de-DE' => 'name_de',
            'fr-FR' => 'name_fr',
            default => 'name_en',
        };
        
        $query = (new Query())
        ->select(['flag_url', $column])
        ->from('flags')
        ->where(['key' => $key]);
        
        if ($date !== null) {
            $query->andWhere([
                'or',
                ['startdatum' => null],
                ['<=', 'startdatum', $date]
            ])->andWhere([
                'or',
                ['enddatum' => null],
                ['>=', 'enddatum', $date]
            ]);
        }
        
        $flag = $query->orderBy(['startdatum' => SORT_DESC])->one();
        
        if (!$flag) {
            return null;
        }
        
        $flagUrl = $flag['flag_url'];
        
        // Falls die URL nicht mit http:// oder https:// beginnt, Prefix ergänzen
        if (!preg_match('~^https?://~', $flagUrl)) {
            $flagUrl = "https://upload.wikimedia.org/wikipedia/" . ltrim($flagUrl, '/');
        }
        
        return Html::tag('span',
            Html::img($flagUrl, [
                'alt' => $flag[$column],
                'style' => 'width: 30px; height: 20px; object-fit: cover; border-radius: 5px; border: 1px solid darkgrey; margin-right: 5px; vertical-align: middle;'
            ]) . Html::encode($flag[$column]),
            [
                'style' => 'display: inline-block; vertical-align: middle;'
            ]
            );
    }
    
    
    public static function getFlagUrl($iocCode, $date = null)
    {
        // Sonderfälle für nicht verfügbare Flaggen
        $specialFlags = [
            'ADL' => "https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Flag_of_Andaluc%C3%ADa.svg/1920px-Flag_of_Andaluc%C3%ADa.svg.png",
            'AST' => "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_Asturias.svg/1920px-Flag_of_Asturias.svg.png",
            'BSK' => "https://upload.wikimedia.org/wikipedia/commons/2/2d/Flag_of_the_Basque_Country.svg",
            'GAL' => "https://upload.wikimedia.org/wikipedia/commons/6/64/Flag_of_Galicia.svg",
            'CAT' => "https://upload.wikimedia.org/wikipedia/commons/c/ce/Flag_of_Catalonia.svg",
            'IEA' => "https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_the_Taliban.svg",
        ];
        
        // Sonderflagge zurückgeben, falls der IOC-Code in der Liste ist
        if (isset($specialFlags[$iocCode])) {
            return Html::img($specialFlags[$iocCode], [
                'alt' => self::getNationname($iocCode),
                'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
            ]);
        }
        
        // Abfrage der Nation anhand des IOC-Codes
        $nation = Nation::findOne(['kuerzel' => $iocCode]);
        
        // Falls keine Nation gefunden wird, gibt es keine Flagge
        if (!$nation || empty($nation->ISO3166)) {
            return null;
        }
        
        $isoCode = strtolower($nation->ISO3166);
        $baseUrl = "https://flagpedia.net/data/flags/w580/";
        $currentFlag = $isoCode . ".png";
        
        // Historische Flaggen-Logik
        $historicalFlags = [
            'iq' => [
                ['start' => '01.01.1991', 'end' => '30.06.2004', 'url' => 'https://example.com/flags/irq_1991_2004.png'],
            ],
            'ba' => [
                ['start' => '31.01.1946', 'end' => '28.02.1992', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/6/61/Flag_of_Yugoslavia_%281946-1992%29.svg'],
                ['start' => '01.03.1992', 'end' => '04.02.1998', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Flag_of_Bosnia_and_Herzegovina_%281992%E2%80%931998%29.svg/1920px-Flag_of_Bosnia_and_Herzegovina_%281992%E2%80%931998%29.svg.png'],
            ],
            'rs' => [
                ['start' => '31.01.1946', 'end' => '26.04.1992', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/6/61/Flag_of_Yugoslavia_%281946-1992%29.svg'],
                ['start' => '27.04.1992', 'end' => '03.02.2003', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg/1920px-Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg.png'],
                ['start' => '04.02.2003', 'end' => '03.06.2006', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg/1920px-Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg.png'],
            ],
            'me' => [
                ['start' => '27.04.1992', 'end' => '03.02.2003', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg/1920px-Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg.png'],
                ['start' => '04.02.2003', 'end' => '03.06.2006', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg/1920px-Flag_of_Serbia_and_Montenegro_%281992%E2%80%932006%29.svg.png'],
            ],
            'ly' => [
                ['start' => '19.11.1977', 'end' => '23.08.2011', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Flag_of_Libya_%281977%E2%80%932011%2C_2-3%29.svg/1280px-Flag_of_Libya_%281977%E2%80%932011%2C_2-3%29.svg.png'],
            ],
            'mm' => [
                ['start' => '03.01.1974', 'end' => '20.10.2010', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Flag_of_Myanmar_%281974%E2%80%932010%29.svg/1920px-Flag_of_Myanmar_%281974%E2%80%932010%29.svg.png'],
                ['start' => '04.01.1948', 'end' => '02.01.1974', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Flag_of_Burma_%281948%E2%80%931974%29.svg/1920px-Flag_of_Burma_%281948%E2%80%931974%29.svg.png'],
            ],
            'za' => [
                ['start' => '31.05.1928', 'end' => '26.04.1994', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/d/dc/Flag_of_South_Africa_%281928-1982%29.svg'],
            ],
            'am' => [
                ['start' => '17.12.1952', 'end' => '23.08.1991', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Flag_of_the_Soviet_Union.svg/1920px-Flag_of_the_Soviet_Union.svg.png'],
            ],

        ];

        // Falls ein Datum angegeben wurde, konvertieren wir es
        $dateTimestamp = null;
        if ($date !== null) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) { // YYYY-MM-DD
                $dateTimestamp = strtotime($date);
            } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) { // DD.MM.YYYY
                $dateTimestamp = strtotime(str_replace('.', '-', $date));
            } elseif (preg_match('/^\d{6}$/', $date)) { // YYYYMM
                $dateTimestamp = strtotime(substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-01');
            }
        }
        
        // Falls ein Datum gegeben ist, prüfen wir, ob es eine historische Flagge gibt
        if ($dateTimestamp !== null && isset($historicalFlags[$isoCode])) {
            foreach ($historicalFlags[$isoCode] as $flag) {
                $startTimestamp = strtotime(str_replace('.', '-', $flag['start']));
                $endTimestamp = strtotime(str_replace('.', '-', $flag['end']));
                if ($dateTimestamp >= $startTimestamp && $dateTimestamp <= $endTimestamp) {
                    return Html::img($flag['url'], [
                        'alt' => self::getNationname($iocCode),
                        'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
                    ]);
                }
            }
        }
        
        // Standard-Flagge zurückgeben, falls keine Sonder- oder historische Flagge zutrifft
        return Html::img($baseUrl . $currentFlag, [
            'alt' => self::getNationname($iocCode),
            'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
        ]);
    }
    
    /**
     * Gibt eine Liste aller verfügbaren Flaggen aus der Datenbank zurück.
     *
     * @return array Assoziatives Array [kuerzel => land_de]
     */
    public static function getAllFlags(): array
    {
        // Nationen mit gültigem ISO3166-Code abrufen
        $flags = (new \yii\db\Query())
        ->select(['kuerzel', 'land_de'])
        ->from('nation')
        ->where(['not', ['ISO3166' => null]]) // Nur Nationen mit gültigen Kürzeln
        ->orderBy(['land_de' => SORT_ASC])   // Optional: Alphabetische Sortierung
        ->all();
        
        // Ergebnis formatieren
        $result = [];
        foreach ($flags as $flag) {
            $result[$flag['kuerzel']] = $flag['land_de'];
        }
        return $result;
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
     * Gibt den Nationskürzel eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Nationskürzel des Vereinswappens.
     */
    public static function getStadionNation($stadionId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalte `land` aus der Club-Tabelle abzurufen
        $sql = "SELECT land FROM stadiums WHERE id = :stadionId";
        
        // Führe die Abfrage aus und hole das Ergebnis
        $land = $db->createCommand($sql)
        ->bindValue(':stadionId', $stadionId)
        ->queryScalar();
        
        // Überprüfen, ob ein Ergebnis gefunden wurde
        if ($land) {
            return $land; // Gebe den Wert der Spalte `land` zurück
        }
    
    }

     /**
     * Gibt den Nationskürzel eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Nationskürzel des Vereinswappens.
     */
    public static function getSpielerNation($spielerId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalte `land` aus der Club-Tabelle abzurufen
        $sql = "SELECT nati1 FROM spieler WHERE id = :spielerId";
        
        // Führe die Abfrage aus und hole das Ergebnis
        $land = $db->createCommand($sql)
        ->bindValue(':spielerId', $spielerId)
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
    
    
    /**
     * Gibt den Namen eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Name des Vereinswappens.
     */
    public static function getStadionName($stadiumId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalte `land` aus der Club-Tabelle abzurufen
        $sql = "SELECT name FROM stadiums WHERE id = :stadiumId";
        
        // Führe die Abfrage aus und hole das Ergebnis
        $name = $db->createCommand($sql)
        ->bindValue(':stadiumId', $stadiumId)
        ->queryScalar();
        
        // Überprüfen, ob ein Ergebnis gefunden wurde
        if ($name) {
            return $name; // Gebe den Wert der Spalte `land` zurück
        }
    }
    
    
    /**
     * Gibt den Namen eines Vereins zurück.
     * @param int|string $clubId Die ID des Vereins.
     * @return string Der Name des Vereinswappens.
     */
    public static function getSpielerName($spielerId)
    {
        // Holen Sie sich die Datenbankverbindung
        $db = \Yii::$app->db;
        
        // SQL-Abfrage, um die Spalten `vorname` und `name` aus der Tabelle `spieler` abzurufen
        $sql = "SELECT vorname, name FROM spieler WHERE id = :spielerId";
        
        // Führe die Abfrage aus und hole das Ergebnis als Array
        $result = $db->createCommand($sql)
        ->bindValue(':spielerId', $spielerId)
        ->queryOne(); // queryOne gibt ein assoziatives Array zurück
        
        // Überprüfen, ob ein Ergebnis gefunden wurde
        if ($result) {
            $vorname = $result['vorname'];
            $name = $result['name'];
            
            // Überprüfen, ob `vorname` gesetzt ist und nicht leer
            if (!empty($vorname)) {
                return $vorname . ' ' . $name;
            }
            return $name; // Nur den Namen zurückgeben, wenn `vorname` nicht gesetzt ist
        }
        
        return null; // Geben Sie null zurück, falls kein Spieler gefunden wurde
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
        if ($turnier == 0 OR $turnier == 42) {
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
        if ($turnier == '') :
            return '';
        endif;
        
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'name_en' : 'name';
        
        $query = (new \yii\db\Query())
        ->select([$column, 'land'])
        ->from(['wettbewerb'])
        ->where(['ID' => $turnier])
        ->scalar();
        
        return $query;
    }
    
    public static function getTurniernameFullname($turnier, $jahr)
    {
        
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'name_en' : 'name';
        
        
        $query = (new \yii\db\Query())
        ->select([$column, 'land'])
        ->from(['wettbewerb'])
        ->where(['ID' => $turnier])
        ->one(); // Ändere scalar() zu one()
        
        if ($query) {
            $turniername = $query[$column] . " " . $jahr;
            if ($turnier >= 500) :
                $turniername .= "/" . ($jahr+1);
            endif;
            return $turniername;
        }
        
        return null; // Fallback, falls kein Datensatz gefunden wird
    }
    
    
    // Funktion, um das passende SVG für die Aktion zu generieren
    public static function getActionSvg($aktion) {
        $svgGrafik = '';
        switch ($aktion) {
            case 'TOR':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="height: 15px;">
    	       	                	<path d="M13.16 10.14L16.37 5.54L12.46 1.89L6.99 3.85L7.17 9L13.16 10.14Z"></path>
    	       	                	<path d="M11.07 19.47L14.02 22.54L19.16 20.34L20 15.8L14.89 15.17L11.07 19.47Z"></path>
    	       	                	<path d="M14.57 0.27L15.79 0.61L16.95 1.07L18.05 1.64L19.09 2.31L20.04 3.09L20.91 3.96L21.68 4.91L22.36 5.94L22.93 7.04L23.39 8.21L23.72 9.42L23.93 10.69L24 12L23.93 13.31L23.72 14.57L23.39 15.79L22.93 16.95L22.36 18.05L21.68 19.09L20.91 20.04L20.04 20.91L19.09 21.68L18.05 22.36L16.95 22.93L15.79 23.39L14.57 23.72L13.31 23.93L12 24L10.99 23.96L10.01 23.83L9.05 23.63L8.12 23.36L7.23 23.01L6.37 22.6L5.55 22.12L4.78 21.58L4.05 20.98L3.37 20.33L2.74 19.63L2.17 18.88L1.66 18.08L1.21 17.25L0.82 16.37L0.76 16.29L0.76 16.21L0.67 15.95L0.58 15.68L0.49 15.41L0.42 15.14L0.34 14.87L0.28 14.59L0.22 14.31L0.17 14.03L0.12 13.75L0.09 13.46L0.05 13.17L0.03 12.88L0.01 12.59L0 12.29L0 12L0.07 10.69L0.27 9.42L0.61 8.21L1.07 7.04L1.64 5.94L2.31 4.91L3.09 3.96L3.96 3.09L4.91 2.31L5.94 1.64L7.04 1.07L8.21 0.61L9.42 0.27L10.69 0.07L12 0L13.31 0.07L14.57 0.27ZM10.96 0.67L10.45 0.73L9.95 0.81L9.46 0.91L8.97 1.03L8.49 1.18L8.02 1.34L7.57 1.53L7.12 1.73L6.68 1.95L6.25 2.19L5.83 2.45L5.43 2.72L5.04 3.01L4.3 4.19L1.46 7.71L1.36 7.98L1.26 8.25L1.17 8.52L1.08 8.8L1.01 9.08L0.94 9.36L0.87 9.64L0.81 9.93L0.76 10.22L0.72 10.51L0.69 10.8L0.66 11.1L0.64 11.4L0.63 11.7L0.62 12L0.62 12.14L0.63 12.29L0.63 12.43L0.64 12.58L0.65 12.72L0.66 12.86L0.67 13.01L0.68 13.15L0.7 13.29L0.72 13.43L0.73 13.57L0.76 13.71L0.78 13.85L0.8 13.99L0.83 14.13L0.9 11.71L4.2 12.84L6.31 18.27L4.1 20.17L4.52 20.56L4.96 20.92L5.41 21.26L5.89 21.58L6.38 21.88L6.88 22.15L7.4 22.4L7.94 22.62L8.48 22.82L9.04 22.98L9.61 23.12L10.2 23.23L10.79 23.31L11.39 23.36L12 23.37L13.21 23.31L14.39 23.12L15.53 22.81L16.61 22.39L17.64 21.87L18.61 21.25L19.5 20.54L20.32 19.74L21.06 18.86L21.71 17.92L22.26 16.9L22.7 15.83L23.04 14.71L23.26 13.54L23.36 12.34L22.19 12.04L20.35 6.85L20.82 4.82L20.57 4.53L20.32 4.25L20.05 3.98L19.78 3.71L19.5 3.45L19.21 3.21L18.91 2.97L18.6 2.74L18.29 2.53L17.97 2.32L17.64 2.13L17.3 1.94L16.96 1.77L16.61 1.6L16.25 1.45L13.46 1.1L12.19 0.63L12.17 0.63L12.16 0.63L12.15 0.63L12.12 0.63L12.07 0.63L12.05 0.62L12.04 0.62L12.02 0.62L12.01 0.62L12 0.62L11.48 0.64L10.96 0.67Z"></path>
    	       	                </svg>';
                break;
            case '11mX':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="height: 15px;"
    	                			data-sentry-element="Svg" data-sentry-component="PenaltyMissed" data-sentry-source-file="icons.tsx">
    	                			<g fill="none" fill-rule="evenodd" data-sentry-element="g" data-sentry-source-file="icons.tsx">
        	                			<path fill="#D0021B" d="m13.159 10.17 3.13-4.652-3.97-3.575L6.897 4l.021.418 5.438 5.612z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
        	                			<path d="M21.452 19.49a12.015 12.015 0 0 0 2.56-7.656C23.897 5.22 18.442-.049 11.826.066a11.924 11.924 0 0 0-6.85 2.298c-.012.009 5.48 5.717 16.476 17.125ZM5.409 2.831A11.284 11.284 0 0 1 11.837.69c.063-.001.125.001.188.001l1.275.444 2.8.308a11.408 11.408 0 0 1 4.612 3.285l-.43 2.033 1.926 5.143 1.177.277a11.305 11.305 0 0 1-2.395 6.831" fill="#C00" fill-rule="nonzero" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
        	                			<path fill="#C00" d="m19.75 17.706.336-2.012-2.513-.264z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
        	                			<g fill="#C00" data-sentry-element="g" data-sentry-source-file="icons.tsx">
            	                			<path d="m7.159 8.297.025.714.682.13zM11.076 19.457l2.939 3.062 4.753-2.031-4.521-4.601z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
            	                			<path d="M4.198 2.91A11.952 11.952 0 0 0 .021 12c0 1.481.27 2.9.763 4.209l-.003.08.065.078C2.592 20.822 6.926 23.98 12 23.98c3.483 0 6.62-1.487 8.808-3.86M.646 12c0-1.513.3-2.957.839-4.278l2.833-3.516L20.35 19.59a.866.866 0 0 1-.097.141c-2.076 2.264-4.946 3.624-8.253 3.624-3.061 0-5.84-1.22-7.885-3.196l2.204-1.893-2.104-5.42-3.29-1.134-.076 2.417c-.132-.69-.203-1.4-.203-2.128Z" fill-rule="nonzero" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
            	                		</g>
            	                		<path d="M21.39 22.03 2.235 2.18" stroke="#C00" stroke-width="2" stroke-linecap="square" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
        	                		</g>
    	                		</svg>';
                break;
            case '11m':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="height: 15px; color: #A6B851; fill: currentColor;">
    	                			<path d="M13.16 10.14L16.37 5.54L12.46 1.89L6.99 3.85L7.17 9L13.16 10.14Z"></path>
    	                			<path d="M11.07 19.47L14.02 22.54L19.16 20.34L20 15.8L14.89 15.17L11.07 19.47Z"></path>
    	                			<path d="M14.57 0.27L15.79 0.61L16.95 1.07L18.05 1.64L19.09 2.31L20.04 3.09L20.91 3.96L21.68 4.91L22.36 5.94L22.93 7.04L23.39 8.21L23.72 9.42L23.93 10.69L24 12L23.93 13.31L23.72 14.57L23.39 15.79L22.93 16.95L22.36 18.05L21.68 19.09L20.91 20.04L20.04 20.91L19.09 21.68L18.05 22.36L16.95 22.93L15.79 23.39L14.57 23.72L13.31 23.93L12 24L10.99 23.96L10.01 23.83L9.05 23.63L8.12 23.36L7.23 23.01L6.37 22.6L5.55 22.12L4.78 21.58L4.05 20.98L3.37 20.33L2.74 19.63L2.17 18.88L1.66 18.08L1.21 17.25L0.82 16.37L0.76 16.29L0.76 16.21L0.67 15.95L0.58 15.68L0.49 15.41L0.42 15.14L0.34 14.87L0.28 14.59L0.22 14.31L0.17 14.03L0.12 13.75L0.09 13.46L0.05 13.17L0.03 12.88L0.01 12.59L0 12.29L0 12L0.07 10.69L0.27 9.42L0.61 8.21L1.07 7.04L1.64 5.94L2.31 4.91L3.09 3.96L3.96 3.09L4.91 2.31L5.94 1.64L7.04 1.07L8.21 0.61L9.42 0.27L10.69 0.07L12 0L13.31 0.07L14.57 0.27ZM10.96 0.67L10.45 0.73L9.95 0.81L9.46 0.91L8.97 1.03L8.49 1.18L8.02 1.34L7.57 1.53L7.12 1.73L6.68 1.95L6.25 2.19L5.83 2.45L5.43 2.72L5.04 3.01L4.3 4.19L1.46 7.71L1.36 7.98L1.26 8.25L1.17 8.52L1.08 8.8L1.01 9.08L0.94 9.36L0.87 9.64L0.81 9.93L0.76 10.22L0.72 10.51L0.69 10.8L0.66 11.1L0.64 11.4L0.63 11.7L0.62 12L0.62 12.14L0.63 12.29L0.63 12.43L0.64 12.58L0.65 12.72L0.66 12.86L0.67 13.01L0.68 13.15L0.7 13.29L0.72 13.43L0.73 13.57L0.76 13.71L0.78 13.85L0.8 13.99L0.83 14.13L0.9 11.71L4.2 12.84L6.31 18.27L4.1 20.17L4.52 20.56L4.96 20.92L5.41 21.26L5.89 21.58L6.38 21.88L6.88 22.15L7.4 22.4L7.94 22.62L8.48 22.82L9.04 22.98L9.61 23.12L10.2 23.23L10.79 23.31L11.39 23.36L12 23.37L13.21 23.31L14.39 23.12L15.53 22.81L16.61 22.39L17.64 21.87L18.61 21.25L19.5 20.54L20.32 19.74L21.06 18.86L21.71 17.92L22.26 16.9L22.7 15.83L23.04 14.71L23.26 13.54L23.36 12.34L22.19 12.04L20.35 6.85L20.82 4.82L20.57 4.53L20.32 4.25L20.05 3.98L19.78 3.71L19.5 3.45L19.21 3.21L18.91 2.97L18.6 2.74L18.29 2.53L17.97 2.32L17.64 2.13L17.3 1.94L16.96 1.77L16.61 1.6L16.25 1.45L13.46 1.1L12.19 0.63L12.17 0.63L12.16 0.63L12.15 0.63L12.12 0.63L12.07 0.63L12.05 0.62L12.04 0.62L12.02 0.62L12.01 0.62L12 0.62L11.48 0.64L10.96 0.67Z"></path>
    	                		</svg>';
                break;
            case 'ET':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="height: 15px; color: #CC0000; fill: currentColor;">
    	                			<path d="M13.16 10.14L16.37 5.54L12.46 1.89L6.99 3.85L7.17 9L13.16 10.14Z"></path>
    	                			<path d="M11.07 19.47L14.02 22.54L19.16 20.34L20 15.8L14.89 15.17L11.07 19.47Z"></path>
    	                			<path d="M14.57 0.27L15.79 0.61L16.95 1.07L18.05 1.64L19.09 2.31L20.04 3.09L20.91 3.96L21.68 4.91L22.36 5.94L22.93 7.04L23.39 8.21L23.72 9.42L23.93 10.69L24 12L23.93 13.31L23.72 14.57L23.39 15.79L22.93 16.95L22.36 18.05L21.68 19.09L20.91 20.04L20.04 20.91L19.09 21.68L18.05 22.36L16.95 22.93L15.79 23.39L14.57 23.72L13.31 23.93L12 24L10.99 23.96L10.01 23.83L9.05 23.63L8.12 23.36L7.23 23.01L6.37 22.6L5.55 22.12L4.78 21.58L4.05 20.98L3.37 20.33L2.74 19.63L2.17 18.88L1.66 18.08L1.21 17.25L0.82 16.37L0.76 16.29L0.76 16.21L0.67 15.95L0.58 15.68L0.49 15.41L0.42 15.14L0.34 14.87L0.28 14.59L0.22 14.31L0.17 14.03L0.12 13.75L0.09 13.46L0.05 13.17L0.03 12.88L0.01 12.59L0 12.29L0 12L0.07 10.69L0.27 9.42L0.61 8.21L1.07 7.04L1.64 5.94L2.31 4.91L3.09 3.96L3.96 3.09L4.91 2.31L5.94 1.64L7.04 1.07L8.21 0.61L9.42 0.27L10.69 0.07L12 0L13.31 0.07L14.57 0.27ZM10.96 0.67L10.45 0.73L9.95 0.81L9.46 0.91L8.97 1.03L8.49 1.18L8.02 1.34L7.57 1.53L7.12 1.73L6.68 1.95L6.25 2.19L5.83 2.45L5.43 2.72L5.04 3.01L4.3 4.19L1.46 7.71L1.36 7.98L1.26 8.25L1.17 8.52L1.08 8.8L1.01 9.08L0.94 9.36L0.87 9.64L0.81 9.93L0.76 10.22L0.72 10.51L0.69 10.8L0.66 11.1L0.64 11.4L0.63 11.7L0.62 12L0.62 12.14L0.63 12.29L0.63 12.43L0.64 12.58L0.65 12.72L0.66 12.86L0.67 13.01L0.68 13.15L0.7 13.29L0.72 13.43L0.73 13.57L0.76 13.71L0.78 13.85L0.8 13.99L0.83 14.13L0.9 11.71L4.2 12.84L6.31 18.27L4.1 20.17L4.52 20.56L4.96 20.92L5.41 21.26L5.89 21.58L6.38 21.88L6.88 22.15L7.4 22.4L7.94 22.62L8.48 22.82L9.04 22.98L9.61 23.12L10.2 23.23L10.79 23.31L11.39 23.36L12 23.37L13.21 23.31L14.39 23.12L15.53 22.81L16.61 22.39L17.64 21.87L18.61 21.25L19.5 20.54L20.32 19.74L21.06 18.86L21.71 17.92L22.26 16.9L22.7 15.83L23.04 14.71L23.26 13.54L23.36 12.34L22.19 12.04L20.35 6.85L20.82 4.82L20.57 4.53L20.32 4.25L20.05 3.98L19.78 3.71L19.5 3.45L19.21 3.21L18.91 2.97L18.6 2.74L18.29 2.53L17.97 2.32L17.64 2.13L17.3 1.94L16.96 1.77L16.61 1.6L16.25 1.45L13.46 1.1L12.19 0.63L12.17 0.63L12.16 0.63L12.15 0.63L12.12 0.63L12.07 0.63L12.05 0.62L12.04 0.62L12.02 0.62L12.01 0.62L12 0.62L11.48 0.64L10.96 0.67Z"></path>
    	                		</svg>';
                break;
            case 'RK':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:#CC0000; height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>';
                break;
            case 'GK':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:rgb(247,216,123); height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>';
                break;
            case 'GRK':
                $svgGrafik = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
    	                			 data-sentry-element="Svg" data-sentry-component="TwoCards" data-sentry-source-file="icons.tsx" style="height: 15px;">
    	                			 <path fill="#F8D94D" d="M6.06 23.98c-.49.1-.89-.2-.99-.59C4.57 21.5.53 6.37.02 4.47c-.1-.49.2-.89.6-.99 0 0 18.912 16.02 18.91 15.95.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                			 <path fill="#C00" d="M.62 3.48C1.9 3.14 12.2.37 13.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82C19.53 19.587.62 3.48.62 3.48z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                		</svg>';
                break;
            case 'AUS':
                $svgGrafik = '<span class="substitutebutton">
                                    <svg style="color: rgb(204,0,0); height: 12px; fill: currentcolor;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        data-sentry-element="Svg" data-sentry-component="ArrowDown" data-sentry-source-file="icons.tsx">
                                        <path d="M9.22 15.87H4l4.09 4.04L12.14 24l4.05-4.09 4.09-4.04h-5.22V0H9.22V15.87z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
                                    </svg>
                               </span>';#
                break;
            case 'EIN':
                $svgGrafik = '<span class="substitutebutton">
                                    <svg style="transform: rotate(180deg); color: rgb(166, 184, 81); height: 12px; fill: currentcolor;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        data-sentry-element="Svg" data-sentry-component="ArrowUp" data-sentry-source-file="icons.tsx">
                                        <path d="M9.22 15.87H4l4.09 4.04L12.14 24l4.05-4.09 4.09-4.04h-5.22V0H9.22V15.87z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
                                    </svg>
                              </span>';
                break;
        }
        return $svgGrafik;
    }
    
    public static function getActionSymbol($spielId, $spielerId) {
        $query = (new \yii\db\Query())
        ->select(['spielID', 'minute', 'aktion', 'spielerID', 'zusatz', 'spieler2ID'])
        ->from('games')
        ->where([
            'spielID' => $spielId,
        ])
        ->andWhere([
            'or',
            ['spielerID' => $spielerId],
            ['and', ['spieler2ID' => $spielerId], ['aktion' => 'AUS']],
        ])
        ->andWhere(['<', 'minute', 200]) // Bedingung für Minuten kleiner als 200
        ->orderBy(['minute' => SORT_ASC]);
        
        // Alle Ergebnisse abrufen
        $aktionen = $query->all();
        
        // Transformation der Daten
        foreach ($aktionen as &$aktion) {
            if ($aktion['aktion'] === 'AUS' && $aktion['spieler2ID'] == $spielerId) {
                $aktion['spielerID'] = $aktion['spieler2ID'];
                $aktion['aktion'] = 'EIN';
            }
        }
        
        return !empty($aktionen) ? $aktionen : null;
    }
    
    // Diese Methode gibt ein Array von Turnieren zurück, gefiltert nach Geschlecht
    public static function getTurniere($gender = 'M') {
        // Beispielhafte Daten für Männer (M) und Frauen (W)
        $turniere = [
            'M' => [
                ['name' => 'Freundschaft', 'jahr' => 2024, 'id' => 0, 'land' => NULL],
            ],
            'W' => [
                ['name' => 'Frauenfreundschaft', 'jahr' => 2024, 'id' => 0, 'land' => NULL],
            ]
        ];
        
        // Gibt das passende Array basierend auf dem Geschlecht zurück
        return $turniere[$gender] ?? [];
    }
    
    public static function getResultColor($isHome, $match) {
        $isWin = ($isHome && $match->tore1 > $match->tore2) || (!$isHome && $match->tore2 > $match->tore1);
        $isDraw = $match->tore1 === $match->tore2;
        return $isWin ? 'text-success' : ($isDraw ? 'text-secondary' : 'text-danger');
    }
    
    // Ersetzt langfristig getNationenOptions
    public static function getCurrentNationsOptions()
    {
        $language = Yii::$app->language;
        $column = match ($language) {
            'en_US' => 'name_en',
            'fr_FR' => 'name_fr',
            default => 'name_de', // Standard: Deutsch
        };
        
        return ArrayHelper::map(
            Flags::find()
            ->select(['key', $column])
            ->where(['enddatum' => null]) // Nur aktuelle Nationen
            ->orderBy([$column => SORT_ASC])
            ->all(),
            'key',
            function ($model) use ($column) {
                return $model[$column] . " - " . $model['key'];
            }
            );
    }
    
    public static function getNationenOptions()
    {
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'land_en' : 'land_de';
        
        return ArrayHelper::map(
            Nation::find()
            ->select(['kuerzel', $column])
            ->from('nation')
            ->where(['not', ['ISO3166' => null]]) // Nur Nationen mit gültigen Kürzeln
            ->orderBy([$column => SORT_ASC])   // Optional: Alphabetische Sortierung
            ->all(), 'kuerzel', $column);
    }
    
    
    public static function getSpielfussOptions()
    {
        return [
            'r' => Yii::t('app', 'Right Foot'),
            'l' => Yii::t('app', 'Left Foot'),
            'b' => Yii::t('app', 'Two-Footed'),
        ];
    }
    
    
    public static function getTypeOptions()
    {
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'name_en' : 'name_de';
        
        return ArrayHelper::map(
            Typ::find()
            ->select(['id', $column])
            ->from('typ')
            ->orderBy([$column => SORT_ASC])   // Optional: Alphabetische Sortierung
            ->all(), 'id', $column);
    }
    

    /**
     * Rendert die Flagge und den Namen des Landes basierend auf der aktuellen Sprache.
     *
     * @param string $countryCode Der Ländercode (ISO 3166-1 Alpha-2).
     * @param array $options Zusätzliche Optionen für das Bild-Tag.
     * @return string Der gerenderte HTML-Code für die Flagge und den Landesnamen.
     */
    public static function renderFlag($countryCode, $name = false, $options = [])
    {
        if (!$countryCode) {
            return '';
        }
        
        // Standard-Optionen für das Flaggenbild
        $defaultImgOptions = [
            'alt' => '',
            'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
        ];
        
        // Zusammenführen der Standard- und benutzerdefinierten Optionen
        $imgOptions = array_merge($defaultImgOptions, $options);
        
        // URL zur Flagge
        $flagUrl = self::getFlagUrl($countryCode);
        if (!$flagUrl) {
            return '';
        }
        
        // Generiere den HTML-Code für die Flagge
        $html = Helper::getFlagUrl($countryCode);
        
        // Landesname entsprechend der aktuellen Sprache, wenn $name = true
        if ($name) {
            $language = Yii::$app->language;
            $column = $language === 'en_US' ? 'land_en' : 'land_de';
            $country = (new \yii\db\Query())
            ->select([$column])
            ->from('nation')
            ->where(['kuerzel' => $countryCode])
            ->scalar();
            
            if ($country) {
                $html .= Html::encode($country);
            }
        }
        
        return $html;
    }
    
    /**
     * Gibt den Namen eines Landes anhand der aktuell eingestellten Sprache zurück.
     *
     * @param string $iocCode Der Ländercode (3 stelliges IOC Kürzel).
     * @return string Der sprachlich abhängige Landesnamen.
     */
    public static function getNationname($iocCode) {
        
        // Landesname entsprechend der aktuellen Sprache
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'land_en' : 'land_de';
        
        $country = (new \yii\db\Query())
        ->select([$column])
        ->from('nation')
        ->where(['kuerzel' => $iocCode])
        ->scalar();
        
        if (!$country) {
            return '';
        }
        
        // Generiere den HTML-Code
        return Html::encode($country);
    }
  
    /**
     * Gibt die Backgroundcolor für eine wechselnden Background zurück
     * @param integer $index Die entsprechende Nummerierung der Zeile
     * @return string Die ermittelte Hintergrundfarbe
     */
    public static function getRowColor($index) {
        
        $colorRowOdd = "#f0f8ff";
        $colorRowEven = "#ffffff";
        
        $color = $index % 2 === 0 ? $colorRowEven : $colorRowOdd;
        
        $backgroundcolor = 'background-color: ' . $color . ' !important;';
        
        return $backgroundcolor;
    }

    public static function getFormattedDate($date) {
       
        if ($date == '') :
            return '';
        endif;
        
        //Sprache ermitteln
        $locale = Yii::$app->language; // 'de', 'en_US', 'en_UK' etc.
        
        // Datum formatieren basierend auf Sprache
        $dateFormat = match($locale) {
            'de' => 'd.m.Y',
            'en_US' => 'm/d/Y',
            'en_UK' => 'd/m/Y',
            default => 'Y-m-d', // Fallback-Format
        };
        
        return DateTime::createFromFormat('Y-m-d', $date)?->format($dateFormat);
        
    }
    
    public static function getFormattedTime($time)
    {
        $locale = Yii::$app->language;
        
        if ($locale === 'de') {
            return Yii::$app->formatter->asTime($time, 'php:H:i');
        } elseif (in_array($locale, ['en_US', 'en_UK'])) {
            $format = $locale === 'en_US' ? 'php:h:i A' : 'php:H:i';
            return Yii::$app->formatter->asTime($time, $format);
        }
        
        return Yii::$app->formatter->asTime($time, 'php:H:i');
    }
    
    /**
     * Gibt eine Liste aller verfügbaren Positionen aus der Datenbank zurück.
     *
     * @return array Assoziatives Array
     */
    public static function getAllPositions(): array
    {
        // Landesname entsprechend der aktuellen Sprache
        $language = Yii::$app->language;
        $column = $language === 'en_US' ? 'positionLang_en' : 'positionLang_de';
                
        // Nationen mit gültigem ISO3166-Code abrufen
        $positions = (new \yii\db\Query())
        ->select(['id', $column])
        ->from('position')
        ->orderBy([$column => SORT_ASC])   // Optional: Alphabetische Sortierung
        ->all();
        
        // Ergebnis formatieren
        $result = [];
        foreach ($positions as $position) {
            $result[$position['id']] = $position[$column];
        }
        return $result;
    }
    
    /**
     * Gibt die Position eines Spielers zurück.
     * @param int|string $positionId Die ID der Position.
     * @return string Der Position des Spielers.
     */
    public static function getPosition($positionId)
    {
        // Nationen mit gültigem ISO3166-Code abrufen
        $position = (new \yii\db\Query())
        ->select(['id', 'positionKurz'])
        ->from('position')
        ->where(['id' => $positionId])   // Optional: Alphabetische Sortierung
        ->all();
        
        return $position[0]['positionKurz'];
        
    }
    
}
?>