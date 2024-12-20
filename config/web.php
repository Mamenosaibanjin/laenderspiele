<?php

require_once __DIR__ . '/../Components/Helper.php';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qasay_quRjCoJpgEtuXg5rQcWAxhWjMQ',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['yii\db\Command::execute'],
                    'logFile' => '@runtime/logs/db.log',
                    
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'spiele/create' => 'spiele/create',
                'spieler/<id:\d+>' => 'spieler/view',
                'club/<id:\d+>' => 'club/view',
                'club/search' => 'club/search', // Neue Regel für die Club-Suche
                'kader/<id:\d+>/<year:\d+>' => 'kader/view',
                'kader/<id:\d+>/<year:\d+>/<turnier:\d+>' => 'kader/view',
                'impressum' => 'impressum/view',
                'index' => 'index/view',
                'spiele/<wettbewerbID:\d+>/<jahr:\d+>/<gruppe:\w*>/<runde:\d*>/<spieltag:\d*>' => 'spiele/view',
                'spiele/<wettbewerbID:\d+>/<jahr:\d+>' => 'spiele/view', // Fallback für minimale Parameter
                'spielbericht/<id:\d+>' => 'spielbericht/view',  // Spielbericht-Ansicht
                'turnier/<wettbewerbID:\d+>/<jahr:\d+>/<gruppe:\w*>/<runde:\d*>/<spieltag:\d*>' => 'turnier/view',
                'turnier/<wettbewerbID:\d+>/<jahr:\d+>' => 'turnier/view', // Fallback für minimale Parameter
                'turnier/search' => 'turnier/search',
                
            ],
        ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
