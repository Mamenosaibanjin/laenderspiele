<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Spieler;
use app\models\SpielerVereinSaison;
use app\models\SpielerLandWettbewerb;

class SpielerController extends Controller
{
    public function actionView($id)
    {
        // Spieler-Daten
        $spieler = Spieler::findOne($id);
        
        // Vereins-Karriere
        $vereinsKarriere = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 0])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        // Jugendvereine
        $jugendvereine = SpielerVereinSaison::find()
        ->where(['spielerID' => $id, 'jugend' => 1])
        ->orderBy(['von' => SORT_DESC])
        ->all();
        
        // Länderspiel-Karriere
        $laenderspiele = SpielerLandWettbewerb::find()
        ->where(['spielerID' => $id])
        ->orderBy(['jahr' => SORT_DESC])
        ->all();
        
        return $this->render('view', [
            'spieler' => $spieler,
            'vereinsKarriere' => $vereinsKarriere,
            'jugendvereine' => $jugendvereine,
            'laenderspiele' => $laenderspiele,
        ]);
    }
}
?>