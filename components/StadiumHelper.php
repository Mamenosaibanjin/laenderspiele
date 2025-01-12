<?php
namespace app\components;

use yii\helpers\Html;
use Yii;

class StadiumHelper
{
    public static function renderEditableRow($form, $stadium, $field, $labelIcon, $options = [], $werteArray = [])
    {
        switch ($field) {
            case 'name':
                $inputs = $form->field($stadium, 'name')->textInput($options)->label(false);
                break;
                
            case 'stadt':
                $inputs = $form->field($stadium, 'stadt')->textInput($options)->label(false);
                break;
                
            case 'land':
                $inputs =
                $form->field($stadium, 'land')->dropDownList(
                Helper::getNationenOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false);
                
                break;
                
            case 'kapazitaet':
                $inputs = $form->field($stadium, 'kapazitaet')->textInput(['type' => 'number'] + $options)->label(false);
                break;
                
            default:
                $inputs = $form->field($stadium, $field)->textInput($options)->label(false);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $inputs)
            );
    }
}
?>