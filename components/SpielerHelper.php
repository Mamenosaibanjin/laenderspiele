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
                
            case 'nati1':
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
        
        if ($spieler->$field == '') : return ''; endif;
        
        $value = $spieler->$field ?? 'Unbekannt';
        switch ($field) {
            case 'geburtstag':
                $value = Yii::$app->formatter->asDate($spieler->geburtstag, 'long');
                break;
                
            case 'nati1':
                $value = !empty($spieler->nati1)
                ? Html::img(Helper::getFlagUrl($spieler->nati1), ['style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) . ' ' . Html::encode($spieler->nati1)
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
    
    public static function renderEditableRowMulti($form, $spieler, $fields, $labelIcon, $options = [])
    {
        $index = $options['index'] ?? 0; // Index aus Optionen holen
        $cells = '';
        
        foreach ($fields as $field) {
            $inputs = '';
            switch ($field) {
                case 'von':
                    $inputs = $form->field($spieler, "[$index]von")->input('month', [
                    'value' => substr($spieler->von, 0, 4) . '-' . substr($spieler->von, 4, 2),
                    'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'bis':
                    $inputs = $form->field($spieler, "[$index]bis")->input('month', [
                    'value' => $spieler->bis ? substr($spieler->bis, 0, 4) . '-' . substr($spieler->bis, 4, 2) : '',
                    'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'verein':
                    $vereine = $options['vereine'] ?? []; // Vereine aus Optionen holen
                    $vereinId = is_object($spieler->verein) ? $spieler->verein->id : $spieler->verein;
                    $vereinName = $vereinId ? Helper::getClubName($vereinId) . ' (' . Helper::getClubNation($vereinId) . ')' : '';
                    
                    $vereinsDaten = json_encode(array_map(function ($verein) {
                        return [
                            'label' => $verein['name'],
                            'value' => $verein['id'],
                            'klarname' => $verein['name'],
                        ];
                    }, $vereine));
                        
                        $inputs =
                        Html::hiddenInput("SpielerVereinSaison[$index][verein]", $vereinId, [
                            'id' => "hidden-verein-id-$index",
                        ]).
                        Html::textInput("[$index]vereinName", $vereinName, [
                            'id' => "autocomplete-verein-$index",
                            'class' => 'form-control',
                            'data-vereine' => $vereinsDaten,
                            'placeholder' => Yii::t('app', 'Search for a club'),
                        ]);
                        break;
                        
                case 'land':
                    $inputs = $form->field($spieler, "[$index]land")->dropDownList(
                    Helper::getNationenOptions(),
                    [
                    'prompt' => Yii::t('app', 'Choose a country'),
                    'class' => 'form-control',
                    ]
                    )->label(false);
                    break;
                    
                case 'position':
                    $positionen = $options['positionen'] ?? []; // Positionen aus Optionen holen
                    //$positionId = $spieler->position; // Aktuelle PositionID
                    $positionId = is_object($spieler->position) ? $spieler->position->id : $spieler->position;
                    
                    $inputs = $form->field($spieler, "[$index]position")->dropDownList(
                        Helper::getAllPositions(),
                        [
                            'prompt' => Yii::t('app', 'Choose a position'),
                            'class' => 'form-control',
                            'options' => [$positionId => ['Selected' => true]] // Vorbelegung setzen
                        ]
                        )->label(false);
                        break;
                    
                case 'buttons':
                    $inputs = 
                    Html::tag('div',
                    Html::checkbox("SpielerVereinSaison[$index][jugend]", $spieler->jugend, [
                    'id' => "jugend-switch-$index",
                    'autocomplete' => 'off',
                    'value' => '1',
                    ]) .
                    Html::label(Yii::t('app', 'Youth'), "jugend-switch-$index", [
                    'class' => 'btn btn-secondary btn-sm',
                    'style' => 'margin-left: 7px;',
                    ]),
                    ['class' => 'btn-group-toggle', 'data-toggle' => 'buttons', 'style' => 'float: left; padding-right: 7px;']
                    ) . " " .
                    Html::SubmitButton(Yii::t('app', 'Save'), [
                    'class' => 'btn btn-primary btn-sm',
                    ]) . 
                    " " . 
                    Html::button(Yii::t('app', 'X'), [
                    'class' => 'btn btn-danger btn-sm',
                    'onclick' => "deleteRow($index)", // Optional: Funktion zum Löschen
                    ]);
                    break;
                    
                default:
                    $inputs = $form->field($spieler, "[$index]$field")->textInput($options)->label(false);
                    break;
            }
            
            $cells .= Html::tag('td', $inputs);
        }
        
        return Html::tag('tr', $cells);
    }
    
    
}
?>