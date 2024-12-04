<?php
namespace app\controllers;

use yii\web\Controller;

class UeberController extends Controller
{
    public function actionView()
    {
        return $this->render('view');
    }
}
?>