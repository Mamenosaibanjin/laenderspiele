<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
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
            // Überprüfen, ob eine Redirect-URL übergeben wurde
            $redirectUrl = Yii::$app->request->post('LoginForm')['redirectUrl'] ?? Yii::$app->homeUrl;
            
            // Umwandlung der absoluten URL in eine relative URL, falls nötig
            /*if (strpos($redirectUrl, Yii::$app->getHomeUrl()) === 0) {
                $redirectUrl = substr($redirectUrl, strlen(Yii::$app->getHomeUrl()));
            }*/
            
            // Sicherstellen, dass die URL korrekt interpretiert wird
            $redirectUrl = Url::to($redirectUrl, true); // Absolute URL generieren
            // Weiterleiten
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
        
        // Redirect auf die ursprüngliche Seite oder Home, falls kein Referrer vorhanden ist
        $redirectUrl = Yii::$app->request->referrer ?? Yii::$app->homeUrl;
        
        // Sicherstellen, dass die URL korrekt interpretiert wird
        $redirectUrl = Url::to($redirectUrl, true); // Absolute URL generieren
        
        return $this->redirect($redirectUrl);
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
