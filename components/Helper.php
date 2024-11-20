<?php

namespace app\components;

class Helper
{
    public static function getFlagUrl($countryCode, $year = null)
    {
        $baseUrl = "https://flagpedia.net/data/flags/w20/";
        $currentFlag = strtolower($countryCode) . ".png";

        // Historische Flaggen-Logik
        if ($year !== null) {
            $historicalFlags = [
                'IRQ' => [
                    ['start' => 1991, 'end' => 2004, 'url' => 'https://example.com/flags/irq_1991_2004.png'],
                ],
            ];

            if (isset($historicalFlags[$countryCode])) {
                foreach ($historicalFlags[$countryCode] as $flag) {
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