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
                $value = Yii::$app->formatter->asDate($spieler->geburtstag, 'long') . ", " . Html::encode($spieler->geburtsort) . " " . Helper::getFlagUrl($spieler->geburtsland, $spieler->geburtstag);
                break;
                
            case 'nati1':
                $value = '';
                if (!empty($spieler->nati1)) {
                    $value .= Helper::getFlagUrl($spieler->nati1) . " ";
                }
                if (!empty($spieler->nati2)) {
                    $value .= Helper::getFlagUrl($spieler->nati2) . " ";
                }
                if (!empty($spieler->nati3)) {
                    $value .= Helper::getFlagUrl($spieler->nati3);
                }
                $value = trim($value) ?: 'Unbekannt';
                break;
                
            case 'homepage':
                $url = Html::encode($spieler->homepage);
                $value = Html::a($url, "http://$url", ['target' => '_blank']);
                break;
                
            case 'facebook':
                $username = Html::encode($spieler->facebook);
                $value = Html::a("$username", "http://www.facebook.com/$username", ['target' => '_blank']);
                break;
                
            case 'instagram':
                $username = Html::encode($spieler->instagram);
                $value = Html::a("$username", "http://www.instagram.com/$username", ['target' => '_blank']);
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
                    $vonValue = $spieler->von ? substr($spieler->von, 0, 4) . '-' . substr($spieler->von, 4, 2) : ''; // Default-Wert bei NULL
                    $inputs = $form->field($spieler, "[$index]von")->input('month', [
                        'value' => $vonValue,
                        'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'bis':
                    $bisValue = $spieler->bis ? substr($spieler->bis, 0, 4) . '-' . substr($spieler->bis, 4, 2) : ''; // Default-Wert bei NULL
                    $inputs = $form->field($spieler, "[$index]bis")->input('month', [
                        'value' => $bisValue,
                        'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'verein':
                    $vereine = $options['vereine'] ?? [];
                    $vereinId = is_object($spieler->verein) ? $spieler->verein->id : ($spieler->verein ?? 0); // Fallback auf 0 bei NULL
                    $vereinName = $vereinId ? Helper::getClubName($vereinId) . ' (' . Helper::getClubNation($vereinId) . ')' : '';
                    
                    $vereinsDaten = json_encode(array_map(function ($verein) {
                        
                        //$land = Helper::getClubNation($verein['id']);  // Dynamisch das Land aus der Helper-Methode holen
                        return [
                            'label' => Html::encode($verein['name']),
                            'value' => $verein['id'],
                            'klarname' => Html::encode($verein['name'] . " (" . $verein['land'] . ")"),
                        ];
                    }, $vereine));
                        
                        $inputs =
                        Html::hiddenInput("SpielerVereinSaison[$index][verein]", $vereinId, [
                            'id' => "hidden-verein-id-$index",
                        ]) .
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
                    $positionen = $options['positionen'] ?? [];
                    $positionId = is_object($spieler->position) ? $spieler->position->id : ($spieler->position ?? ''); // Fallback auf leeren String bei NULL
                    
                    $inputs = $form->field($spieler, "[$index]position")->dropDownList(
                        Helper::getAllPositions(),
                        [
                            'prompt' => Yii::t('app', 'Choose a position'),
                            'class' => 'form-control',
                            'options' => [$positionId => ['Selected' => true]], // Vorbelegung setzen
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
                        Html::submitButton(Yii::t('app', 'Save'), [
                        'class' => 'btn btn-primary btn-sm',
                        ]) .
                        " " .
                        Html::checkbox("SpielerVereinSaison[$index][delete]", false, [
                        'id' => "delete-switch-$index",
                        'value' => '1',
                        'class' => 'd-none',
                        ]) .
                        Html::button(Yii::t('app', 'X'), [
                        'class' => 'btn btn-danger btn-sm',
                        'onclick' => "$('#delete-switch-$index').prop('checked', true); $(this).closest('form').submit();",
                        'data-confirm' => Yii::t('app', 'Möchten Sie diesen Eintrag wirklich löschen?'),
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
    
           
    public static function renderViewRowMulti($karriereDaten, $fields, $options = [])
    {
        $rows = '';
        $index = $options['index'] ?? 0; // Fallback für Index
        
        foreach ($karriereDaten as $daten) {
            $cells = '';
            
            foreach ($fields as $field) {
                $value = '';
                switch ($field) {
                    case 'zeitraum':
                        $von = isset($daten['von']) ? substr($daten['von'], 0, 4) . '/' . substr($daten['von'], 4, 2) : '';
                        $bis = isset($daten['bis']) ? substr($daten['bis'], 0, 4) . '/' . substr($daten['bis'], 4, 2) : '';
                        $value = "$von - $bis";
                        break;
                        
                    case 'verein':
                        $verein = $daten['verein'] ?? null;
                        $vereinId = is_object($verein) ? $verein->id : $verein; // ID aus dem Objekt extrahieren
                        $value = $vereinId
                        ? Html::img(Helper::getClubLogoUrl($vereinId), ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']) .
                        Html::a(Html::encode(Helper::getClubName($vereinId)), ['/club/view', 'id' => $vereinId], ['class' => 'text-decoration-none'])
                        : Yii::t('app', 'Unknown Club');
                        break;
                        
                    case 'land':
                        $verein = $daten['verein'] ?? null;
                        $vereinId = is_object($verein) ? $verein->id : $verein; // ID aus dem Objekt extrahieren
                        $nation = $vereinId ? Helper::getClubNation($vereinId) : null;
                        $value = $nation
                        ? Helper::getFlagUrl($nation, $daten['von'])
                        : Yii::t('app', 'Unknown Country');
                        break;
                        
                    case 'position':
                        $position = $daten['position'] ?? null;
                        $positionId = is_object($position) ? $position->id : $position; // ID aus dem Objekt extrahieren
                        $value = $positionId ? Helper::getPosition($positionId) : Yii::t('app', 'Unknown Position');
                        break;
                        
                    default:
                        $value = $daten[$field] ?? Yii::t('app', 'Unknown');
                        break;
                }
                
                $cells .= Html::tag('td', $value);
            }
            
            $rows .= Html::tag('tr', $cells);
            $index++;
        }
        
        return $rows;
    }
    
    
}
?>