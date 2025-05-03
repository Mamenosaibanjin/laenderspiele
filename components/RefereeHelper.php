<?php
namespace app\components;

use app\components\Helper;
use Yii;
use yii\db\Query;
use yii\helpers\Html;

class RefereeHelper
{
    public static function renderEditableRow($form, $referee, $field, $labelIcon, $options = [])
    {
        $inputs = '';
        switch ($field) {
            case 'geburtstag':
                
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($referee, 'geburtstag')->input('date', [
                'value' => $referee->geburtstag ?: '',
                ])->label(false) .
                $form->field($referee, 'geburtsort')->textInput([])->label(false) .
                
                $form->field($referee, 'geburtsland')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
            case 'nati1':
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($referee, 'nati1')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($referee, 'nati2')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($referee, 'nati3')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
            default:
                $inputs = $form->field($referee, $field)->textInput($options)->label(false);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $inputs)
            );
    }

    public static function renderViewRow($field, $referee, $labelIcon = null)
    {
        
        if ($referee->$field == '') : return ''; endif;
        
        $value = $referee->$field ?? 'Unbekannt';
        switch ($field) {
            case 'geburtstag':
                $birthdate = $referee->geburtstag ?: null; // Falls kein Geburtsdatum vorhanden, bleibt es null
                $countryFlag = Helper::getFlagInfo($referee->geburtsland, $birthdate ?? date('Y-m-d'));
                
                if ($birthdate) {
                    $value = Yii::$app->formatter->asDate($birthdate, 'long') . ", " . Html::encode($referee->geburtsort) . " " . $countryFlag;
                } else {
                    $value = Html::encode($referee->geburtsort) . " " . $countryFlag;
                }
                break;
                
            case 'nati1':
                $value = '';
                foreach (['nati1', 'nati2', 'nati3'] as $field) {
                    if (!empty($referee->$field)) {
                        $value .= Helper::getFlagInfo($referee->$field, date('Y-m-d')) . "<br>";
                    }
                }
                $value = trim($value) ?: 'Unbekannt';
                break;
                
            default:
                $value = Html::encode($value);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $value)
            );
    }    
}
?>