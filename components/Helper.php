<?php

namespace app\components;

use app\models\Nation;

class Helper
{
    public static function getFlagUrl($iocCode, $year = null)
    {
        // Abfrage der Nation anhand des IOC-Codes
        $nation = Nation::findOne(['kuerzel' => $iocCode]);
        if (!$nation) {
            return null; // Kein ISO-Code gefunden, keine Flagge verfügbar
        }
        
        $isoCode = strtolower($nation->ISO3166);
        $baseUrl = "https://flagpedia.net/data/flags/w20/";
        $currentFlag = $isoCode . ".png";
        
        // Historische Flaggen-Logik
        if ($year !== null) {
            $historicalFlags = [
                'IRQ' => [
                    ['start' => 1991, 'end' => 2004, 'url' => 'https://example.com/flags/irq_1991_2004.png'],
                ],
            ];

            if (isset($historicalFlags[$isoCode])) {
                foreach ($historicalFlags[$isoCode] as $flag) {
                    if ($year >= $flag['start'] && $year <= $flag['end']) {
                        return $flag['url'];
                    }
                }
            }
        }

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
}
?>