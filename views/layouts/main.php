<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\components\Helper;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>

<?php 
// JavaScript für das Scroll-Event einfügen
$scrollJs = <<<JS
document.addEventListener('scroll', function () {
    const header = document.getElementById('header');
    const logo = document.getElementById('header-logo');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
        logo.src = '/projects/laenderspiele2.0/yii2-app-basic/web/assets/img/logo_short.png'; // Neues Logo
    } else {
        header.classList.remove('scrolled');
        logo.src = '/projects/laenderspiele2.0/yii2-app-basic/web/assets/img/logo_header.png'; // Ursprüngliches Logo
    }
});
JS;
//$this->registerJs($scrollJs, \yii\web\View::POS_READY); // noch ausgeklammert, da Flackern erzeugt 
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <div class="logo-container text-center">
        <?= Html::a(
            Html::img(Yii::getAlias('@web/assets/img/logo_header.png'), [
                'alt' => 'Logo',
                'id' => 'header-logo',
                'class' => 'img-fluid',
            ]),
            Yii::$app->homeUrl
        ) ?>
    </div>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Navigationsmenü -->
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav'], // Navigation zentriert
                'items' => [
                    ['label' => 'Home', 'url' => ['/site/index']],
                    [
                        'label' => 'Wettbewerbe Männer:', 
                        'items' => array_merge(
                            array_map(function($turnier) {
                                return [
                                    'label' => $turnier['name'] . ' ' . $turnier['jahr'], // Anzeige des Turniers
                                    'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                                ];
                            }, Helper::getTurniere('M'))
                        ),
                    ],
                    [
                        'label' => 'Wettbewerbe Frauen:',
                        'items' => array_merge(
                            array_map(function($turnier) {
                                return [
                                    'label' => $turnier['name'] . ' ' . $turnier['jahr'], // Anzeige des Turniers
                                    'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                                ];
                            }, Helper::getTurniere('W'))
                        ),
                    ],
                ],
            ]) ?>

            <!-- Suchformular am rechten Rand -->
            <form class="d-flex ms-auto" action="<?= Url::to(['/search/index']) ?>" method="get">
    <div class="input-group" style="width: 15%; position: absolute; top: 10px; right: 15px;" >
        <input class="form-control me-2" type="search" placeholder="Suche" aria-label="Suche" name="q" style="margin-right: 0 !important;">
        <span class="input-group-text"><i class="fa fa-search"></i></span>
    </div>
    <button class="btn btn-outline-light" type="submit" style="display: none;"></button>
</form>

        </div>
    </nav>
</header>




<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">
                &copy; 4-go.de <?= date('Y') ?>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <?= Nav::widget([
                    'options' => ['class' => 'nav justify-content-end'],
                    'items' => [
                        ['label' => 'Kontakt', 'url' => ['/contact']],
                        ['label' => 'Impressum', 'url' => ['/impressum']],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
