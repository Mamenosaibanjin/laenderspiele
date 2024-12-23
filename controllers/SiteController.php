<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::info('Test: ActionIndex aufgerufen', __METHOD__);
        
        $model = new LoginForm();
        $this->view->params['loginModel'] = $model; // $model übergeben
        
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            // Überprüfen, ob eine Redirect-URL übergeben wurde, andernfalls zur Startseite weiterleiten
            $redirectUrl = Yii::$app->request->post('LoginForm')['redirectUrl'] ?? Yii::$app->homeUrl;
            
            // Umwandlung der absoluten URL in eine relative URL, falls nötig
            if (strpos($redirectUrl, Yii::$app->getHomeUrl()) === 0) {
                // Entferne den Basis-URL-Teil, um die relative URL zu erhalten
                $redirectUrl = substr($redirectUrl, strlen(Yii::$app->getHomeUrl()));
            }
            
            // Falls kein Redirect-URL gesetzt ist, zur Startseite weiterleiten
            if (empty($redirectUrl)) {
                $redirectUrl = Yii::$app->homeUrl;
            }

            // Weiterleiten auf die angegebene URL (oder zur Startseite, wenn keine URL angegeben ist)
            return $this->redirect($redirectUrl);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    public function actionAjaxLogin()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return ['success' => true, 'reload' => true]; // Erfolg und Reload-Anweisung
        }
        
        return ['success' => false, 'errors' => $model->getErrors()]; // Login fehlgeschlagen
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Yii::$app->request->url);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    public function actionGeneratePassword()
    {
        $plainPassword = '#Leana20!!20'; // Ersetze dies durch das gewünschte Passwort
        $hashedPassword = Yii::$app->security->generatePasswordHash($plainPassword);
        echo $hashedPassword . "<br>";
        $authKey = Yii::$app->security->generateRandomString();
        echo $authKey;
        die;
    }
    
    public function actionRegister()
    {
        $model = new \app\models\RegisterForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('success', 'Registrierung erfolgreich! Sie können sich jetzt einloggen.');
            return $this->redirect(['login']);
        }
        
        return $this->render('register', [
            'model' => $model,
        ]);
    }
    
}
