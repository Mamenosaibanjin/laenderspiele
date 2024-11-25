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
    
}
?>