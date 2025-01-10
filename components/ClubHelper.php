<?php
namespace app\components;

use DateTime;
use Yii;
use yii\helpers\Html;
use PhpParser\Node\Expr\Cast\Object_;

class ClubHelper
{
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
     * Rendert eine Zeile mit Div-Containern für die Anzeige des Landes.
     *
     * @param string|null $country Der IOC Code des Landes.
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
    public static function renderCountryDivRow($country)
    {
        if (!$country) {
            return '';
        }
        
        // HTML für die Div-Struktur erstellen
        $iconDiv = Html::tag('div', Html::tag('i', '', ['class' => 'fas fa-earth-europe']), [
            'class' => 'col-2',
            'style' => 'text-align: right;',
        ]);
        
        $flagDiv = Html::tag('div', Helper::renderFlag($country, true), [
            'class' => 'col-10',
            'style' => 'text-align: left;',
        ]);
        
        return $iconDiv . $flagDiv;
    }
     
     /**
     * Rendert eine Zeile mit Div-Containern für eine Homepage.
     *
     * @param string|null $homepage Die URL der Homepage.
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
    public static function renderFoundationDivRow($founded)
    {
        if (!$founded) {
            return '';
        }
        
        
        //Sprache ermitteln
        $locale = Yii::$app->language; // 'de', 'en_US', 'en_UK' etc.
        
        // Datum formatieren basierend auf Sprache
        $dateFormat = match($locale) {
            'de' => 'd.m.Y',
            'en_US' => 'm/d/Y',
            'en_UK' => 'd/m/Y',
            default => 'Y-m-d', // Fallback-Format
        };
        
        $formattedDate = DateTime::createFromFormat('Y-m-d', $founded)?->format($dateFormat);
        
        $iconDiv = Html::tag('div', Html::tag('i', '', ['class' => 'fas fa-calendar-alt']), [
            'class' => 'col-2',
            'style' => 'text-align: right; padding-top: 10px;',
        ]);
        
        $dateDiv = Html::tag('div', Html::encode($formattedDate), [
            'class' => 'col-10',
            'style' => 'text-align: left; padding-top: 10px;',
        ]);
        
        return $iconDiv . $dateDiv;
    }
    
        
     /**
     * Rendert eine Zeile mit Div-Containern für eine Homepage.
     *
     * @param string|null $homepage Die URL der Homepage.
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
    public static function renderStadiumDivRow($stadium)
    {
        if (!$stadium) {
            return '';
        }
        
        $iconDiv = Html::tag('div', Html::tag('i', '', ['class' => 'fas fa-location-dot']), [
            'class' => 'col-2',
            'style' => 'text-align: right; padding-top: 10px;',
        ]);
        
        $stadiumDiv = Html::tag('div', Html::encode($stadium->name), [
            'class' => 'col-10',
            'style' => 'text-align: left; padding-top: 10px;',
        ]);
        
        return $iconDiv . $stadiumDiv;
    }
    
    /**
     * Rendert eine Tabellenzeile mit einem Homepage-Link.
     *
     * @param string|null $homepage Die URL der Homepage.
     * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
     */
    public static function renderHomepageTableRow($homepage, $labelIcon) {
        if (!$homepage) {
            return '';
        }
        
        // Überprüfen, ob die URL mit "http" beginnt, andernfalls "http://" hinzufügen
        $url = strpos($homepage, 'http') === 0 ? $homepage : 'http://' . $homepage;
        
        // HTML für die Tabellenzeile erstellen
        return Html::tag('tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', Html::a($homepage, $url, ['target' => '_blank']))
            );
    }
    
    /**
     * Rendert eine Tabellenzeile mit dem Kurznamen des Vereins.
     *
     * @param string|null $name Der Kurzame des Vereins.
     * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
     */
    public static function renderClubNameRow($name, $labelIcon) {
        if (!$name) {
            return '';
        }
        
        // HTML für die Tabellenzeile erstellen
        return Html::tag('tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', Html::encode($name))
            );
    }
    
     /**
     * Rendert eine Tabellenzeile mit dem Langnamen des Vereins.
     *
     * @param string|null $name Der Langame des Vereins.
     * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
     */
    public static function renderClubFullnameRow($name, $labelIcon) {
        if (!$name) {
            return '';
        }
        
        // HTML für die Tabellenzeile erstellen
        return Html::tag('tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', Html::encode($name))
            );
    }

    /**
     * Rendert eine Tabellenzeile mit dem Land des Vereins.
     *
     * @param string|null $name Der IOC-Code des Landes des Vereins.
     * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
     */
    public static function renderClubNationRow($iocCode, $labelIcon) {
        if (!$iocCode) {
            return '';
        }
        
        // HTML für die Tabellenzeile erstellen
        return Html::tag('tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', Helper::renderFlag($iocCode, true))
            );
    }
    
    
    /**
     * Rendert eine Zeile mit einem Farb-Circle für die Clubfarben.
     * 
     * @param string|null $colors Die Farben des Vereins
     * @return string Der gerenderte HTML-Code für die Div-Zeile oder ein leerer String.
     */
    public static function renderColorCircle($colors, $labelIcon)
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
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', $output)
             );
     }
     
     /**
      * Rendert eine Tabellenzeile mit den Stadion-Informationen.
      *
      * @param Object|null $stadium Das Stadionobjekt.
      * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
      */
     public static function renderStadiumTableRow($stadium, $labelIcon) 
     {
         
         if (!$stadium) {
             return '';
         }
         
         return Html::tag('tr',
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', Html::encode($stadium->name) . '<br>' . Yii::t('app', 'Capacity') . ': ' . Html::encode($stadium->kapazitaet))
             );
     }
     
     /**
      * Rendert eine Tabellenzeile mit den Adress-Informationen.
      *
      * @param Object|null $club Das Clubobjekt.
      * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
      */
     public static function renderAddressRow($club, $labelIcon) 
     {
         
         if (!$club) {
             return '';
         }
         
         $output = Html::encode($club->name) . '<br>';
         $output .= $club->postfach ? 'Postfach ' . Html::encode($club->postfach) . '<br>' : '';
         $output .= $club->strasse ? nl2br(Html::encode($club->strasse)) . '<br>' : '';
         $output .= $club->ort ? Html::encode($club->ort) . '<br>' : '';
         $output .= Helper::getNationname($club->land);
         
         return Html::tag('tr',
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', $output)
             );
     }
     
     /**
      * Rendert eine Tabellenzeile mit der Telefonnummer.
      *
      * @param String|null $phonenumber Die telefonnumer des Vereins.
      * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String.
      */
     public static function renderPhonenumberRow($phonenumber, $labelIcon) 
     {
         
         if (!$phonenumber) {
             return '';
         }
         
         return Html::tag('tr',
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', Html::encode($phonenumber))
             );
     }
     
     /** Gibt das Gründungsdatum des vereins zurück.
      * 
      * @param String|null $date Das Gründungsdatum des Vereins
      * @return string Der gerenderte HTML-Code für die Tabellenzeile oder ein leerer String. Datumsformat je nach Sprachauswahl
      */
     public static function renderFoundationdateRow($datum, $labelIcon)
     {
         if (!$datum) {
             return '';
         }
         
         //Sprache ermitteln
         $locale = Yii::$app->language; // 'de', 'en_US', 'en_UK' etc.
         
         // Datum formatieren basierend auf Sprache
         $dateFormat = match($locale) {
             'de' => 'd.m.Y',
             'en_US' => 'm/d/Y',
             'en_UK' => 'd/m/Y',
             default => 'Y-m-d', // Fallback-Format
         };
         
         $formattedDate = DateTime::createFromFormat('Y-m-d', $datum)?->format($dateFormat);
         
         if (!$formattedDate) {
             return ''; // Falls das Datum ungültig ist
         }
         
         return Html::tag(
             'tr',
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', Html::encode($formattedDate))
             );
     }
     
     /** Gibt die Clubinformationen (name, fullname, land) eines Vereins zurück
      * 
      * @param Object|null $club Das Club-Object
      * @return String Der gerenderte HTML-Code für die Tabellenzeile  oder ein leerer String
      */
     public static function renderEditableRow($form, $club, $field, $labelIcon, $options = [], $werteArray = [])
     {
         switch ($field) {
             case 'address':
                 $inputs =
                 $form->field($club, 'postfach')->textInput(['maxlength' => true])->label(Yii::t('app', 'PO Box')) .
                 $form->field($club, 'strasse')->textInput(['maxlength' => true])->label(Yii::t('app', 'Street')) .
                 $form->field($club, 'ort')->textInput(['maxlength' => true])->label(Yii::t('app', 'City'));
                 
                 break;
                 
             case 'stadium':
                 $stadien = $werteArray;
                 $inputs =
                 $form->field($club, 'stadionID')->hiddenInput([
                 'id' => 'hidden-stadion-id',
                 'value' => $club->stadionID,
                 ])->label(false) .
                 Html::textInput('stadionName', $club->getStadionName(), [
                 'id' => 'autocomplete-stadion',
                 'class' => 'form-control',
                 'data-stadien' => json_encode(array_map(function ($stadion) {
                 return [
                     'label' => $stadion['name'] . ', ' . $stadion['stadt'],
                     'value' => $stadion['id'],
                     'klarname' => $stadion['name']
                 ];
                 }, $stadien)),
                 ]) .
                 ButtonHelper::newStadiumButton();
                 
                 break;
                 
             case 'colors':
                 $farbenArray = explode('-', $club->farben);
                 $inputs = '<div id="farben-container">';
                 foreach ($farbenArray as $index => $farbe) {
                     $inputs .= Html::textInput("farben[]", $farbe, [
                         'class' => 'form-control farbe-input',
                         'data-index' => $index
                     ]);
                 }
                 $inputs .= '</div><br>' .
                     ButtonHelper::addColorButton() .
                     '<br><p><em>' . Yii::t('app', 'Double-Click a color to remove it.') . '</em></p>';
                     
                     break;
                     
             case 'nations':
                 $inputs =
                 $form->field($club, 'land')->dropDownList(
                 Helper::getNationenOptions(),
                 [
                 'prompt' => Yii::t('app', 'Choose a country'),
                 'class' => 'form-control'
                     ]
                 )->label(false);
                 
                 break;
                 
             case 'founded':
                 // Datum im Format Y-m-d bereitstellen, da HTML5-Eingabefeld "date" dieses Format benötigt
                 $dateValue = $club->founded ?: ''; // Wenn kein Datum gesetzt ist, bleibt das Feld leer
                 
                 $inputs = $form->field($club, 'founded')->input('date', [
                     'value' => $dateValue,
                     'maxlength' => true,
                 ])->label(Yii::t('app', 'Founded'));
                 break;
                 
                 default:
                 // Standardfall für einfache Felder
                 $inputs = $form->field($club, $field)->textInput($options)->label(false);
                 break;
         }
         
         return Html::tag(
             'tr',
             Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
             Html::tag('td', $inputs)
             );
     }
     
     
     public static function renderViewRow($field, $value, $labelIcon = null)
     {
         switch ($field) {
             case 'name':
                 return ClubHelper::renderClubNameRow($value->name, $labelIcon);
             case 'namevoll':
                 return ClubHelper::renderClubFullnameRow($value->namevoll, $labelIcon);
             case 'nations':
                 return ClubHelper::renderClubNationRow($value->land, $labelIcon);
             case 'founded':
                 return ClubHelper::renderFoundationdateRow($value->founded, $labelIcon);
             case 'colors':
                 return ClubHelper::renderColorCircle($value->farben, $labelIcon);
             case 'stadium':
                 return ClubHelper::renderStadiumTableRow($value, $labelIcon);
             case 'address':
                 return ClubHelper::renderAddressRow($value, $labelIcon);
             case 'telefon':
                 return ClubHelper::renderPhonenumberRow($value->telefon, $labelIcon);
             case 'homepage':
                 return ClubHelper::renderHomepageTableRow($value->homepage, $labelIcon);
             default:
                 // Standardfall für einfache Felder
                 return '';
         }
     }

}
?>