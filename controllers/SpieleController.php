<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Turnier;
use app\components\Helper; // Falls Helper für getTurniername() genutzt wird

class SpieleController extends Controller
{
    public function actionView($wettbewerbID, $jahr, $gruppe = null, $runde = null, $spieltag = null)
    {
        // Daten aus der Tabelle "turnier" holen
        $spiele = Turnier::findTurniere($wettbewerbID, $jahr, $gruppe, $runde, $spieltag);
        $turniername = Helper::getTurniername($wettbewerbID); // Wettbewerbsname holen
        
        return $this->render('view', [
            'spiele' => $spiele,
            'turniername' => $turniername,
            'jahr' => $jahr,
        ]);
    }
}
?>