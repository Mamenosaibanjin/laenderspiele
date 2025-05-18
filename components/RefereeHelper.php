<?php
namespace app\components;

use app\components\Helper;
use app\models\Runde;
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

    public static function getGamesAtTournament($refereeID, $tournamentID)
    {
        $spiele = (new \yii\db\Query())
        ->select(['turnier.datum', 'turnier.zeit', 'spiele.id', 'spiele.club1ID', 'spiele.club2ID', 'spiele.tore1', 'spiele.tore2', 'spiele.extratime', 'spiele.penalty'])
        ->from('spiele')
        ->innerJoin('turnier', 'turnier.spielID = spiele.id')
        ->where(['spiele.referee1ID' => $refereeID])
        ->andWhere(['turnier.tournamentID' => $tournamentID])
        ->all();
        
        $spieleTabelle = "<div style='padding: 15px; background: #ffffff; border: 1px dashed #ccc;'>";
        $spieleTabelle .= "<table class='table'>";
        $spieleTabelle .= "<tbody>";
        
        $counter = 0;
        
        foreach ($spiele as $spiel):
        
        $backgroundStyle = $counter++ % 2 === 0 ? '#f0f8ff;' : '#ffffff;';
        $spieleTabelle .= "<tr>";
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 20%;'>";
        $spieleTabelle .= Helper::getFormattedDate($spiel['datum']) . " - " . Helper::getFormattedTime($spiel['zeit']);
        $spieleTabelle .= "</td>";
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 25%;'>";
        $spieleTabelle .= StadiumHelper::getGespielteGruppe($refereeID, $spiel['id']);
        $spieleTabelle .= "</td>";
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 20%; text-align: right;'>";
        $spieleTabelle .= Html::a(Helper::getClubName($spiel['club1ID']), ['club/view', 'id' => $spiel['club1ID']], ['class' => 'text-decoration-none']);
        $spieleTabelle ." </td>";
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 5%; text-align: center;'>";
        $spieleTabelle .= "-";
        $spieleTabelle .= "</td>";
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 20%; text-align: left;'>";
        $spieleTabelle .= Html::a(Helper::getClubName($spiel['club2ID']), ['club/view', 'id' => $spiel['club2ID']], ['class' => 'text-decoration-none']);
        $spieleTabelle .= " </td>";
        
        $ergebnis = $spiel['tore1'] . ":" . $spiel['tore2'];
        if ($spiel['extratime'] == 1) {
            $ergebnis .= ' n.V.';
        } elseif ($spiel['penalty'] == 1) {
            $ergebnis .= ' i.E.';
        }
        
        $spieleTabelle .= "<td style='background-color: " . $backgroundStyle . " width: 10%; text-align: left;'>";
        $spieleTabelle .= Html::a($ergebnis, ['spielbericht/view', 'id' => $spiel['id']], ['class' => 'text-decoration-none']);
        $spieleTabelle .=" </td>";
        $spieleTabelle .= "</tr>";
        
        
        endforeach;
        $spieleTabelle .= "</tbody>";
        $spieleTabelle .= "</table>";
        
        $spieleTabelle .= "</div>";
        
        return $spieleTabelle;
    }
    
    public static function getGespielteGruppe($stadionID, $spielID)
    {
        $gruppe = Runde::find()
        ->select('name')
        ->innerJoin('turnier', 'turnier.rundeID = runde.id')
        ->where(['spielID' => $spielID])
        ->all();
        
        if ($gruppe) {
            return $gruppe[0]['name'];
        } else {
            return "";
        }
    }
}
?>