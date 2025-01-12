<?php
use app\components\ButtonHelper;
use app\components\Helper;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $stadium app\models\Stadion */
/* @var $matches yii\data\ActiveDataProvider */
/* @var $isEditing bool */
/* @var $teams array */

$this->title = $isEditing
? ($stadium->isNewRecord ? Yii::t('app', 'Create New Stadium') : Yii::t('app', 'Edit Stadium: {name}', ['name' => $stadium->name]))
: $stadium->name;
?>

<div class="stadium-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-3">
        <!-- Widget 1: Stadiondaten -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3><?= Yii::t('app', 'Stadium Data') ?></h3>
                </div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                        <table class="table">
                            <tr>
                                <td><i class="fas fa-landmark"></i> <?= $form->field($stadium, 'name')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-city"></i> <?= $form->field($stadium, 'stadt')->textInput(['maxlength' => true])->label(false) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-flag"></i> <?= $form->field($stadium, 'land')->dropDownList(Helper::getCountryList())->label(false) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-users"></i> <?= $form->field($stadium, 'kapazitaet')->textInput(['type' => 'number'])->label(false) ?></td>
                            </tr>
                        </table>
                        <div class="form-group">
                            <?= ButtonHelper::saveButton() ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                            <tr>
                                <td><i class="fas fa-landmark"></i> <?= Html::encode($stadium->name) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-city"></i> <?= Html::encode($stadium->stadt) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-flag"></i> <?= Helper::renderFlag($stadium->land, true) ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-users"></i> <?= Yii::$app->formatter->asInteger($stadium->kapazitaet) ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-users"></i> <?= Yii::t('app', 'Teams') ?>: 
                                    <?= implode(', ', array_map(fn($team) => Html::encode($team->name), $teams)) ?>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Widget 2: Termine & Ergebnisse -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3><?= Yii::t('app', 'Matches and Results') ?></h3>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $matches,
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                            [
                                'attribute' => 'wettbewerb',
                                'label' => Yii::t('app', 'Competition'),
                                'value' => 'Bundesliga',
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'datum',
                                'label' => Yii::t('app', 'Date'),
                                'value' => '2024-04-04',
                            ],
                            [
                                'attribute' => 'heimteam',
                                'label' => Yii::t('app', 'Home Team'),
                                'value' => fn($model) => Html::encode($model->club1->name),
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'auswaertsteam',
                                'label' => Yii::t('app', 'Away Team'),
                                'value' => fn($model) => Html::encode($model->club2->name),
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'ergebnis',
                                'label' => Yii::t('app', 'Result'),
                                'value' => function ($model) {
                                    $result = "{$model->tore1} : {$model->tore2}";
                                    if ($model->extratime) $result .= ' n.V.';
                                    if ($model->penalty) $result .= ' i.E.';
                                    return Html::a($result, ['/spielbericht/' . $model->id], ['target' => '_blank']);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'zuschauer',
                                'label' => Yii::t('app', 'Spectators'),
                                'value' => fn($model) => Yii::$app->formatter->asInteger($model->zuschauer),
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
