<?php
namespace app\components;

use yii\helpers\Html;
use app\models\Runde;
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
    
    public static function getGamesAtTournament($stadionID, $tournamentID) 
    {
        $spiele = (new \yii\db\Query())
        ->select(['turnier.datum', 'turnier.zeit', 'spiele.id', 'spiele.club1ID', 'spiele.club2ID', 'spiele.tore1', 'spiele.tore2', 'spiele.extratime', 'spiele.penalty'])
        ->from('spiele')
        ->innerJoin('turnier', 'turnier.spielID = spiele.id')
        ->where(['spiele.stadiumID' => $stadionID])
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
            $spieleTabelle .= StadiumHelper::getGespielteGruppe($stadionID, $spiel['id']);
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