<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Games;
use app\models\Spiel;
use app\models\Spieler;
use app\models\SpielerVereinSaison;
use app\models\SpielerLandWettbewerb;

class SpielberichtController extends Controller
{
    public function actionView($id) {
        $spiel = Spiel::findOne($id);
        
        if (!$spiel) {
            throw new NotFoundHttpException('Spiel nicht gefunden.');
        }
        
        $aufstellung1 = $spiel->aufstellung1;
        $aufstellung2 = $spiel->aufstellung2;
        
        $highlightAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['TOR', '11m', 'ET', 'RK', 'GRK']])
        ->andWhere(['<', 'minute', 200]) // Bedingung für Minuten kleiner als 200
        ->orderBy(['minute' => SORT_ASC])
        ->all();
                
        $toreAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['TOR', '11m', 'ET', '11mX']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $kartenAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['GK', 'RK', 'GRK']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselHeim = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->andWhere(['zusatz' => ['H']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $wechselAuswaerts = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['AUS']])
        ->andWhere(['zusatz' => ['A']])
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
        $besondereAktionen = Games::find()
        ->where(['spielID' => $spiel->id])
        ->andWhere(['aktion' => ['11mX']])
        ->andWhere(['<', 'minute', 200]) // Bedingung für Minuten kleiner als 200
        ->orderBy(['minute' => SORT_ASC])
        ->all();
        
       
        return $this->render('view', [
            'spiel' => $spiel,
            'aufstellung1' => $aufstellung1,
            'aufstellung2' => $aufstellung2,
            'highlightAktionen' => $highlightAktionen,
            'toreAktionen' => $toreAktionen,
            'kartenAktionen' => $kartenAktionen,
            'wechselAktionen' => $wechselAktionen,
            'besondereAktionen' => $besondereAktionen,
            'wechselHeim' => $wechselHeim,
            'wechselAuswaerts' => $wechselAuswaerts,
        ]);
    }
}
