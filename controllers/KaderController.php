<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use app\components\Helper;
use app\models\Club;
use app\models\SpielerLandWettbewerb;
use app\models\Tournament;

class KaderController extends Controller
{
    public function actionView($tournamentID, $id)
    {
        $club = Club::findOne($id);
        if (!$club) {
            throw new NotFoundHttpException('Seite nicht gefunden.');
        }
        
        $tournament = Tournament::findOne($tournamentID);
        
        if (Helper::isNationalTeam($club)) {
            if (!$tournament) {
                throw new NotFoundHttpException('Turnier nicht gefunden.');
            }
            $squad = $club->getNationalSquad($club->id, $tournament->wettbewerbID, $tournament->jahr);
        } else {
            if (!$tournament) {
                // Dummy-Objekt erzeugen, damit die View funktioniert
                $tournament = new Tournament();
                $tournament->jahr = $tournamentID; // Setze wenigstens das Jahr
                $tournament->startdatum = $tournamentID . '-07-01'; // Typische Startdatum-Annäherung
            }
            $squad = $club->getSquad($club->id, $tournamentID);
        }
        
        return $this->render('view', [
            'club' => $club,
            'squad' => $squad,
            'tournament' => $tournament,
            'tournamentID' => $tournamentID,
        ]);
    }
    
}
?>