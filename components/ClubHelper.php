<?php
namespace app\components;

use yii\helpers\Html;

class ClubHelper
{
    /**
     * Rendert eine Tabellenzeile mit einem Homepage-Link.
     *
     * @param string|null $homepage Die URL der Homepage.
     * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
     */
    public static function renderHomepageTableRow($homepage) {
        if (!$homepage) {
            return '';
        }
        
        // Überprüfen, ob die URL mit "http" beginnt, andernfalls "http://" hinzufügen
        $url = strpos($homepage, 'http') === 0 ? $homepage : 'http://' . $homepage;
        
        // HTML für die Tabellenzeile erstellen
        return Html::tag('tr',
            Html::tag('th', Html::tag('i', '', ['class' => 'fas fa-laptop-code'])) .
            Html::tag('td', Html::a($homepage, $url, ['target' => '_blank']))
            );
    }

    /**
     * Rendert eine Zeile mit Div-Containern für eine Homepage.
     *
     * @param string|null $homepage Die URL der Homepage.
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
    public static function renderHomepageDivRow($homepage)
    {
        if (!$homepage) {
            return '';
        }
        
        // Überprüfen, ob die URL mit "http" beginnt, andernfalls "http://" hinzufügen
        $url = strpos($homepage, 'http') === 0 ? $homepage : 'http://' . $homepage;
        
        // HTML für die Div-Struktur erstellen
        $iconDiv = Html::tag('div', Html::tag('i', '', ['class' => 'fas fa-laptop-code']), [
            'class' => 'col-2',
            'style' => 'text-align: right; padding-top: 10px;',
        ]);
        
        $linkDiv = Html::tag('div', Html::a($homepage, $url, ['target' => '_blank']), [
            'class' => 'col-10',
            'style' => 'text-align: left; padding-top: 10px;',
        ]);
        
        return $iconDiv . $linkDiv;
    }
    
    /**
     * Rendert eine Zeile mit einem Farb-Circle für die Clubfarben.
     * 
     * @param string|null $colors Die Farben des Vereins
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
     public static function renderColorCircle($colors)
     {
         if (!$colors) {
             return '';
         }
         
         // Farben aufteilen und letzten Index bestimmen
         $colorList = explode('-', $colors);
         $lastIndex = count($colorList) - 1;
         
         // HTML für die Farbkreise erstellen
         $output = '';
         foreach ($colorList as $index => $color) {
             $backgroundColor = strpos($color, '#') === 0 ? $color : Html::encode(self::colorToHex($color));
             
             $style = "
                display:inline-block;
                width:20px;
                height:20px;
                background-color:{$backgroundColor};
                border:1px solid #000;
                " . ($index === 0 ? 'border-radius: 10px 0 0 10px;' : '') .
                ($index === $lastIndex ? 'border-radius: 0 10px 10px 0;' : '') .
                ($index !== $lastIndex ? 'margin-right: -5px;' : '');
                
                $output .= Html::tag('span', '', ['style' => $style]);
         }
         
         return Html::tag('tr',
             Html::tag('th', Html::tag('i', '', ['class' => 'fas fa-palette'])) .
             Html::tag('td', $output)
             );
     }
}
?>