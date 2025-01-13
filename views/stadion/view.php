<?php
use app\components\ButtonHelper;
use app\components\Helper;
use app\components\StadiumHelper;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\components\GameHelper;
use app\controllers\SpielberichtController;
use app\controllers\TurnierController;
use app\controllers\SpieleController;

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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3><?= Yii::t('app', 'Stadium Data')?></h3>
                </div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                        <table class="table">
                            <?= StadiumHelper::renderEditableRow($form, $stadium, 'name', 'fas fa-landmark', ['maxlength' => true]) ?>
                            <?= StadiumHelper::renderEditableRow($form, $stadium, 'stadt', 'fas fa-city', ['maxlength' => true]) ?>
                            <?= StadiumHelper::renderEditableRow($form, $stadium, 'land', 'fas fa-flag') ?>
                            <?= StadiumHelper::renderEditableRow($form, $stadium, 'kapazitaet', 'fas fa-users', ['type' => 'number']) ?>
                        </table>
                        <div class="form-group">
                            <?= ButtonHelper::saveButton() ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                            <tr>
                                <th><i class="fas fa-landmark"></i></th>
                                <td><?= Html::encode($stadium->name) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-city"></i></th>
                                <td><?= Html::encode($stadium->stadt) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-flag"></i></th>
                                <td><?= Helper::renderFlag($stadium->land, true) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-users"></i></th>
                                <td><?= Yii::$app->formatter->asInteger($stadium->kapazitaet) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-shield-alt"></i></th>
                                <td>
                                    <b><?= Yii::t('app', 'Teams') ?></b> 
                                    <?= implode(', ', array_map(fn($team) => '<br>' . Html::encode($team->name), $teams)) ?>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Widget 2: Termine & Ergebnisse -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><?= Yii::t('app', 'Matches and Results') ?></h3>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $matches,
                        'summary' => false,
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                            [
                                'attribute' => 'wettbewerb',
                                'label' => Yii::t('app', 'Competition'),
                                'value' => fn($model) => Helper::getTurniername($model->turnier->wettbewerb->id ?? '') ?? '',
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'datum',
                                'label' => Yii::t('app', 'Date'),
                                'value' => fn($model) => Helper::getFormattedDate($model->turnier->datum ?? '') ?? '',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'heimteam',
                                'label' => Yii::t('app', 'Home'),
                                'value' => fn($model) => Html::encode($model->club1->name),
                                'format' => 'raw',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'auswaertsteam',
                                'label' => Yii::t('app', 'Away'),
                                'value' => fn($model) => Html::encode($model->club2->name),
                                'format' => 'raw',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'ergebnis',
                                'label' => Yii::t('app', 'Result'),
                                'value' => function ($model) {
                                    $result = "{$model->tore1}:{$model->tore2}";
                                    if ($model->extratime) $result .= Yii::t('app', 'a.e.t.');
                                    if ($model->penalty) $result .= Yii::t('app', 'p.s.o.');
                                    return Html::a($result, ['/spielbericht/' . $model->id], ['target' => '_blank']);
                                },
                                'format' => 'raw',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'zuschauer',
                                'label' => Yii::t('app', 'Spectators'),
                                'value' => fn($model) => Yii::$app->formatter->asInteger($model->zuschauer),
                                'enableSorting' => false,
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
