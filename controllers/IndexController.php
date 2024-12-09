<?php
namespace app\controllers;

use yii\web\Controller;

class IndexController extends Controller
{
    public function actionView()
    {
        return $this->render('view');
    }
}
?>