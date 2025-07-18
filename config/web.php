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

                'referee/new' => 'referee/new',
                'referee/search' => 'referee/search',
                
                'club/new' => 'club/new',
                'club/<id:\d+>' => 'club/view',
                'club/search' => 'club/search', // Neue Regel für die Club-Suche
                'stadion/new' => 'stadion/new',
                'stadion/<id:\d+>' => 'stadion/view',
                'stadion/search' => 'stadion/search',
                
                'search/<query:[^/]+>' => 'search/view',
                
                'kader/<tournamentID:\d+>/<id:\d+>' => 'kader/view',
                'impressum' => 'impressum/view',
                'index' => 'index/view',
                
                'spiele/<tournamentID:\d+>/<gruppe:\w*>/<runde:\d*>/<spieltag:\d*>' => 'spiele/view',
                'spiele/<tournamentID:\d+>' => 'spiele/view', // Fallback für minimale Parameter
                
                'spielbericht/<id:\d+>' => 'spielbericht/view',  // Spielbericht-Ansicht
                'spielbericht/speichern-info' => 'spielbericht/speichern-info',  // Spielbericht-Ansicht
                
                'aufstellung/spieler-suche' => 'aufstellung/spieler-suche',
                'aufstellung/spieler-aufstellung-suche' => 'aufstellung/spieler-aufstellung-suche',
                'aufstellung/speichern' => 'aufstellung/speichern',
                
                'turnier/<tournamentID:\d+>/ergebnisse/<action:(new|update|delete)>' => 'turnier/ergebnisse-<action>',
                'turnier/<tournamentID:\d+>/ergebnisse/<rundeID:\d*>' => 'turnier/ergebnisse',
                'turnier/<tournamentID:\d+>/ergebnisse' => 'turnier/ergebnisse',
                'turnier/<tournamentID:\d+>/spiele-im-stadion/<stadionID:\d*>' => 'turnier/spiele-im-stadion',
                'turnier/<tournamentID:\d+>/schiedsrichter-spiele/<refereeID:\d*>' => 'turnier/schiedsrichter-spiele',
                'turnier/<tournamentID:\d+>/spieler/<positionen:[0-9,]*>/<sort:[a-zA-Z0-9\-]+>/<page:\d+>' => 'turnier/spieler',
                'turnier/<tournamentID:\d+>/torjaeger' => 'turnier/torjaeger',
                'turnier/<tournamentID:\d+>/stadien/<sort:[a-zA-Z0-9\-]+>/<page:\d+>' => 'turnier/stadien',
                'turnier/<tournamentID:\d+>/schiedsrichter/<sort:[a-zA-Z0-9\-]+>/<page:\d+>' => 'turnier/schiedsrichter',
                'turnier/<tournamentID:\d+>/spielplan/<landID:\d*>' => 'turnier/spielplan',
                'turnier/<tournamentID:\d+>/spielplan/' => 'turnier/spielplan',
                'turnier/<tournamentID:\d+>/teilnehmer/' => 'turnier/teilnehmer',
                
                'turnier/<tournamentID:\d+>/archiv/' => 'turnier/archiv',
                'turnier/<tournamentID:\d+>/alle-sieger/' => 'turnier/alle-sieger',
                'turnier/<tournamentID:\d+>/alle-torjaeger/' => 'turnier/alle-torjaeger',
                'turnier/<tournamentID:\d+>/statistik/tore-pro-saison' => 'turnier/tore-pro-saison',
                'turnier/<tournamentID:\d+>/statistik/tore-pro-runde' => 'turnier/tore-pro-runde',
                'turnier/<tournamentID:\d+>/statistik/hoechste-siege' => 'turnier/hoechste-siege',
                'turnier/<tournamentID:\d+>/statistik/torreichste-spiele' => 'turnier/torreichste-spiele',
                'turnier/<tournamentID:\d+>/statistik/meiste-tore-eines-spielers' => 'turnier/meiste-tore-eines-spielers',
                'turnier/<tournamentID:\d+>/statistik/unfairste-spiele' => 'turnier/unfairste-spiele',
                
                'turnier/<wettbewerbID:\d+>/<action:(archiv|statistik|alle-sieger|torschuetzenkoenige)>' => 'turnier/<action>',
                'turnier/search' => 'turnier/search',
                'turnier/anlegen' => 'turnier/anlegen',
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
