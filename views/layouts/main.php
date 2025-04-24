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

$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerMetaTag(['name' => 'csrf-param', 'content' => Yii::$app->request->csrfParam]);
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>

<?php 
// JavaScript für das Scroll-Event einfügen
/*$scrollJs = <<<JS
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
});*/

//JS;
//$this->registerJs($js, \yii\web\View::POS_READY); // Das JavaScript wird jetzt korrekt eingebunden
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

<header id="main-header" class="bg-light text-white">
    <!-- Zeile 1: Logo, Titel, Login -->
    <div class="container-fluid header-container">
        <div class="d-flex align-items-center logo-container">
            <?= Html::a(
                Html::img('@web/assets/img/logo_header.png', [
                    'alt' => 'Logo',
                    'id' => 'main-logo',
                    'class' => 'me-3 logo-img',
                ]),
                Yii::$app->homeUrl
            ) ?>
        </div>
        <div class="login-container">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    Yii::$app->user->isGuest
                        ? [
                            'label' => 'Login',
                            'url' => '#',
                            'linkOptions' => [
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#loginModal',
                                'class' => 'btn btn-turnier nav-link nav-link-login',
                            ],
                        ]
                        : [
                            'label' => Yii::$app->user->identity->username,
                            'items' => [
                                [
                                    'label' => 'Logout',
                                    'url' => ['/site/logout'],
                                    'linkOptions' => ['data-method' => 'post'],
                                ],
                            ],
                        ],
                ],
            ]) ?>
        </div>
    </div>

     <!-- Zeile 2: Home, Männer/Women Turniere + Suche -->
    <div class="container-fluid py-2 border-bottom d-flex justify-content-between align-items-center bg-secondary header-container">
        <div class="d-flex">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav flex-row'],
                'items' => [
                    [
                        'label' => 'Home',
                        'url' => ['/site/index'],
                        'linkOptions' => ['class' => 'btn btn-wettbewerbe nav-link nav-link-home']
                    ],
                    [
                        'label' => 'Wettbewerbe Männer',
                        'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                        'items' => array_map(function ($turnier) {
                            return [
                                'label' => $turnier['name'] . ' ' . $turnier['jahr'],
                                'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                            ];
                        }, Helper::getTurniere('M')),
                    ],
                    [
                        'label' => 'Wettbewerbe Frauen',
                        'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                        'items' => array_map(function ($turnier) {
                            return [
                                'label' => $turnier['name'] . ' ' . $turnier['jahr'],
                                'url' => ['/turnier/' . $turnier['id'] . '/' . $turnier['jahr'] . '/' . ($turnier['land'] ?? '')],
                            ];
                        }, Helper::getTurniere('W')),
                    ],
                ],
            ]) ?>
        </div>
    
        <!-- Suchleiste rechts mit integriertem Icon -->
        <form class="input-group search-container" action="<?= Url::to(['/search/index']) ?>" method="get">
            <input type="search" class="form-control" name="q" placeholder="Suche" aria-label="Suche">
            <button class="btn btn-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>


    <!-- Zeile 3: Turnier- und Statistik-Dropdowns -->
    <div class="container-fluid py-2 bg-light">
        <div class="d-flex">
        
        	<?php  $turnier = Helper::getCurrentTurnierParams(); ?>
            
            <div class="dropdown">
                <?= $this->render('//layouts/_turnierMenu', ['turnier' => $turnier]) ?>
            </div>
        </div>
    </div>
</header>


<header id="sticky-header" class="bg-light text-white d-none fixed-top shadow-sm" style="z-index: 1030; transition: opacity 0.4s ease;">
    <div class="py-2 d-flex justify-content-between align-items-center">
        
        <!-- LINKS: Logo + Wettbewerbsname -->
        <div class="d-flex align-items-center">
            <?= Html::a(
                Html::img('@web/assets/img/logo_short.png', [
                    'alt' => 'Logo',
                    'style' => 'height: 30px;',
                    'class' => 'me-2',
                ]),
                Yii::$app->homeUrl
            ) ?>
            <?php
                $turnier = Helper::getCurrentTurnierParams();
                if ($turnier): ?>
                    <span class="text-blue fw-bold">
                        <?= Helper::getTurniernameFullname($turnier['wettbewerbID'], $turnier['jahr']) ?>
                    </span>
                <?php endif; ?>

        </div>

        <!-- MITTE: Dropdowns -->
        <div class="d-flex">

            <div class="dropdown">
				<?= $this->render('//layouts/_turnierMenu', ['turnier' => $turnier]) ?>
            </div>
        </div>

        <!-- RECHTS: Suchfeld + Login -->
        <div class="d-flex align-items-center">
            <form class="d-flex me-2" action="<?= Url::to(['/search/index']) ?>" method="get">
                <div class="input-group input-group-sm">
                    <input class="form-control" type="search" placeholder="Suche" name="q">
                    <button class="btn btn-search" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>

            <div class="login-container">
                <?= Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => [
                        Yii::$app->user->isGuest
                            ? [
                                'label' => 'Login',
                                'url' => '#',
                                'linkOptions' => [
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#loginModal',
                                    'class' => 'btn btn-turnier nav-link nav-link-login',
                                ],
                            ]
                            : [
                                'label' => Yii::$app->user->identity->username,
                                'items' => [
                                    [
                                        'label' => 'Logout',
                                        'url' => ['/site/logout'],
                                        'linkOptions' => ['data-method' => 'post'],
                                    ],
                                ],
                            ],
                    ],
                ]) ?>
            </div>
        </div>

    </div>
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


<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
				<?php $loginModel = $this->params['loginModel'] ?? new \app\models\LoginForm(); // Fallback ?>

                <?php if ($loginModel): ?>
                    <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'login-form', // WICHTIG: Diese ID wird im JS verwendet
                    'action' => ['/site/login'], // Verweise auf die neue AJAX-Action
                ]); ?>
                        <?= $form->field($loginModel, 'username')->textInput(['autofocus' => true]) ?>
                        <?= $form->field($loginModel, 'password')->passwordInput() ?>
                        <?= $form->field($loginModel, 'rememberMe')->checkbox() ?>
                        <?= $form->field($loginModel, 'redirectUrl')->hiddenInput(['value' => Yii::$app->request->url])->label(false); ?>
                        
                        <div class="form-group">
                            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                        </div>
                    <?php \yii\widgets\ActiveForm::end(); ?>
                <?php else: ?>
                    <p>Login-Formular konnte nicht geladen werden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stickyHeader = document.getElementById('sticky-header');
    const mainHeader = document.getElementById('main-header');
    const offset = mainHeader.offsetHeight;

    window.addEventListener('scroll', function () {
        if (window.scrollY > offset) {
            stickyHeader.classList.add('visible', 'd-block');
            stickyHeader.classList.remove('d-none');
        } else {
            stickyHeader.classList.remove('visible', 'd-block');
            stickyHeader.classList.add('d-none');
        }
    });
});
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
