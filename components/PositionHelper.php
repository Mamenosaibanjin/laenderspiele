<?php
namespace app\components;

use Yii;

class PositionHelper
{
    public static function getMapping(array $positionKurzList): array
    {
        // Aktuelle Sprache ermitteln
        $currentLanguage = Yii::$app->language;
        
        // Spalte dynamisch basierend auf der Sprache festlegen
        $langColumn = $currentLanguage === 'de' ? 'positionLang_de' : 'positionLang_en';
        
        // Datenbankabfrage, um die relevanten Positionen zu laden
        $positions = (new \yii\db\Query())
        ->select(['id', 'positionKurz', $langColumn])
        ->from('position')
        ->where(['positionKurz' => $positionKurzList])
        ->all();
        
        // Mapping-Array erstellen
        $mapping = [];
        foreach ($positions as $position) {
            $mapping[$position['id']] = Yii::t('app', $position[$langColumn]);
        }
        
        return $mapping;
    }
}

?>