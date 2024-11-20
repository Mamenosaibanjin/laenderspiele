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
}
?>