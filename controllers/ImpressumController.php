<?php 
namespace app\controllers;

use yii\web\Controller;

class ImpressumController extends Controller
{
    public function actionView()
    {
        return $this->render('view');
    }
}
?>