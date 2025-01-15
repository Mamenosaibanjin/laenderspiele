<?php
namespace app\components;

use yii\helpers\Html;

class ButtonHelper
{
    /**
     * Generiert einen standardisierten Button.
     *
     * @param string $label Der Text des Buttons.
     * @param string $url Die URL, auf die der Button verweist (optional).
     * @param array $options Zusätzliche HTML-Optionen für den Button.
     * @return string Der generierte Button-HTML-Code.
     */
    public static function createButton($label, $url = null, $options = [])
    {
        $defaultOptions = [
            'class' => 'btn btn-primary', // Standardklasse
        ];
        
        // Optionen zusammenführen
        $options = array_merge($defaultOptions, $options);
        
        // Wenn eine URL angegeben ist, wird ein Link-Button erstellt
        if ($url) {
            return Html::a($label, $url, $options);
        }
        
        // Andernfalls ein Standard-Button
        return Html::button($label, $options);
    }
    
    /**
     * Shortcut für einen "Speichern"-Button.
     *
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Speichern"-Button-HTML-Code.
     */
    public static function saveButton($options = [])
    {
        return self::createButton(\Yii::t('app', 'Save'), null, array_merge(['type' => 'submit'], $options));
    }
    
    /**
     * Shortcut für einen "Details Speichern"-Button.
     *
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Speichern"-Button-HTML-Code.
     */
    public static function saveDetailsButton($options = [])
    {
        return self::createButton(\Yii::t('app', 'Save Details'), null, array_merge(['type' => 'submit'], $options));
    }
    
    /**
     * Shortcut für einen "Abbrechen"-Button.
     *
     * @param string $url Die URL, auf die verwiesen wird.
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Abbrechen"-Button-HTML-Code.
     */
    public static function cancelButton($url, $options = [])
    {
        return self::createButton(\Yii::t('app', 'Cancel'), $url, array_merge(['class' => 'btn btn-secondary'], $options));
    }
    
    /**
     * Shortcut für einen "Löschen"-Button.
     *
     * @param string $url Die URL, auf die verwiesen wird.
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Löschen"-Button-HTML-Code.
     */
    public static function deleteButton($url, $options = [])
    {
        $defaultOptions = [
            'class' => 'btn btn-danger',
            'data-confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ];
        return self::createButton(\Yii::t('app', 'Delete'), $url, array_merge($defaultOptions, $options));
    }
    
    /**
     * Shortcut für einen "Neu Farbe"-Button.
     *
     * @param string $url Die URL, auf die verwiesen wird.
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Löschen"-Button-HTML-Code.
     */
    
    
    public static function addColorButton($options = [])
    {
        $defaultOptions = [
            'class' => 'btn btn-secondary',
            'id' => 'add-color',
        ];
        return self::createButton(\Yii::t('app', 'Add color'), null, array_merge($defaultOptions, $options));
    }
    
    /**
     * Shortcut für einen "Neues Stadion"-Button.
     *
     * @param string $url Die URL, auf die verwiesen wird.
     * @param array $options Zusätzliche Optionen für den Button.
     * @return string Der generierte "Löschen"-Button-HTML-Code.
     */
    public static function newStadiumButton($options = [])
    {
        $defaultOptions = [
            'class' => 'btn btn-secondary mt-2',
            'id' => 'btn-neues-stadion',
            'onClick' => 'window.open("../stadion/new", "_blank")',
        ];
        return self::createButton(\Yii::t('app', 'Add new Stadium'), null, array_merge($defaultOptions, $options));
    }
    
}

?>