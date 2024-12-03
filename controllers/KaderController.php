<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Club;

class KaderController extends Controller
{
    public function actionView($id, $year)
    {
        $club = Club::findOne($id);
        if (!$club) {
            throw new \yii\web\NotFoundHttpException('Der Club wurde nicht gefunden.');
        }

        // Squad für das gegebene Jahr abrufen
        $squad = $club->getSquad($id, $year);
        return $this->render('view', [
            'club' => $club,
            'jahr' => $year,
            'squad' => $squad,
        ]);
    }
}
?>