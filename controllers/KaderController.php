<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use app\models\Club;
use app\models\SpielerLandWettbewerb;

class KaderController extends Controller
{
    public function actionView($id, $year, $turnier = null)
    {
        $club = Club::findOne($id);
        if (!$club) {
            throw new \yii\web\NotFoundHttpException('Der Club wurde nicht gefunden.');
        }
        
        if ($turnier !== null) {
            
            // Squad für das das Turnier abrufen
            $squad = $club->getNationalSquad($id, $turnier, $year);
        } else {    
#            // Squad für das gegebene Jahr abrufen
            $squad = $club->getSquad($id, $year);
        }
        
        $spielerLandWettbewerb = new SpielerLandWettbewerb();
        
        return $this->render('view', [
            'club' => $club,
            'jahr' => $year,
            'squad' => $squad,
            'turnier' => $turnier,
            'spielerLandWettbewerb' => $spielerLandWettbewerb,
            
        ]);
        
    }
}
?>