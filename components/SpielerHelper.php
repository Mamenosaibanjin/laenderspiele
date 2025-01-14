<?php
namespace app\components;

use app\components\Helper;
use Yii;
use yii\helpers\Html;

class SpielerHelper
{
    public static function renderEditableRow($form, $spieler, $field, $labelIcon, $options = [])
    {
        $inputs = '';
        switch ($field) {
            case 'geburtstag':
                
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($spieler, 'geburtstag')->input('date', [
                'value' => $spieler->geburtstag ?: '',
                ])->label(false) .
                $form->field($spieler, 'geburtsort')->textInput([])->label(false) .
                
                $form->field($spieler, 'geburtsland')->dropDownList(
                Helper::getNationenOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
            case 'nationalitaet':
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($spieler, 'nati1')->dropDownList(
                Helper::getNationenOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($spieler, 'nati2')->dropDownList(
                Helper::getNationenOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($spieler, 'nati3')->dropDownList(
                Helper::getNationenOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
                
            case 'spielfuss':
                
                $inputs = $form->field($spieler, 'spielfuss')->dropDownList(
                Helper::getSpielfussOptions(),
                [
                'prompt' => Yii::t('app', 'Choose Foot'),
                'class' => 'form-control'
                    ]
                )->label(false);
                break;
                
            default:
                $inputs = $form->field($spieler, $field)->textInput($options)->label(false);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $inputs)
            );
    }

    public static function renderViewRow($field, $spieler, $labelIcon = null)
    {
        $value = $spieler->$field ?? 'Unbekannt';
        switch ($field) {
            case 'geburtstag':
                $value = Yii::$app->formatter->asDate($spieler->geburtstag, 'long');
                break;
                
            case 'nationalitaet':
                $value = !empty($spieler->nati1)
                ? Html::img(Helper::getFlagUrl($spieler->nati1), ['style' => 'width: 20px;']) . ' ' . Html::encode($spieler->nati1)
                : 'Unbekannt';
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