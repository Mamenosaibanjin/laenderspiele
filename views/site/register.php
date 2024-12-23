<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RegisterForm $model */

$this->title = 'Registrierung';
?>
<div class="site-register">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Bitte füllen Sie die folgenden Felder aus, um sich zu registrieren:</p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Registrieren', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
