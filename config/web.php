<?php

require_once __DIR__ . '/../Components/Helper.php';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'language' => 'de-DE',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'bootstrap5'], // Hier 'bootstrap5' hinzufügen
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'bootstrap5' => [
            'class' => 'yii\bootstrap5\BootstrapAsset', // Bootstrap5-Asset laden
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qasay_quRjCoJpgEtuXg5rQcWAxhWjMQ',
            'enableCsrfValidation' => false,
            
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
                    'logFile' => '@runtime/logs/app.log',
                    
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'spielbericht/update-lineup' => 'spielbericht/update-lineup',
                'spieler/search-for-lineup/<spielID:\d+>/<type:H|A>' => 'spieler/search-for-lineup',
                'spiele/update-datetime' => 'spiele/update-datetime',
                'spiele/create' => 'spiele/create',
                'spiele/delete' => 'spiele/delete',
                'spieler/reload-career-table' => 'spieler/reload-career-table',
                'spieler/new' => 'spieler/view',
                'spieler/save-details' => 'spieler/save-details',
                'spieler/save-club' => 'spieler/save-club', // Neue Route für das Speichern von Clubs
                'spieler/save-youth' => 'spieler/save-youth',
                'spieler/save-nation' => 'spieler/save-nation',
                'spieler/<id:\d+>' => 'spieler/view',
                'spieler/search' => 'spieler/search',
                'club/new' => 'club/new',
                'club/<id:\d+>' => 'club/view',
                'club/search' => 'club/search', // Neue Regel für die Club-Suche
                'stadion/new' => 'stadion/new',
                'stadion/<id:\d+>' => 'stadion/view',
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
                'site/login' => 'site/login',
                'site/logout' => 'site/logout',
                'spieler-land-wettbewerb/add' => 'spieler-land-wettbewerb/add',
                
                // Regel zur Entfernung von "site"
                'site/<action:\w+>/<id:\d*>' => '<action>',
                'site/<action:\w+>' => '<action>',
                
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
