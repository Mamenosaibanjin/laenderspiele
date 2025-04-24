<?php
use yii\bootstrap5\Nav;
use yii\helpers\Url;

echo Nav::widget([
    'options' => ['class' => 'navbar-nav flex-row'],
    'items' => [
        [
            'label' => 'Turnier',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                ['label' => 'Übersicht', 'url' => ['turnier/index', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Ergebnisse / Tabelle', 'url' => ['turnier/ergebnisse', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spielplan', 'url' => ['turnier/spielplan', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Teilnehmer', 'url' => ['turnier/teilnehmer', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spieler', 'url' => ['turnier/spieler', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torjäger', 'url' => ['turnier/torjaeger', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Stadien', 'url' => ['turnier/stadien', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Schiedsrichter', 'url' => ['turnier/schiedsrichter', 'wettbewerbID' => $turnier['wettbewerbID'], 'jahr' => $turnier['jahr']], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
        [
            'label' => 'Statistik',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                ['label' => 'Archiv', 'url' => ['turnier/archiv', 'wettbewerbID' => $turnier['wettbewerbID']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Statistik', 'url' => ['turnier/statistik', 'wettbewerbID' => $turnier['wettbewerbID']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Alle Sieger', 'url' => ['turnier/alle-sieger', 'wettbewerbID' => $turnier['wettbewerbID']], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torschützenkönige', 'url' => ['turnier/torschuetzenkoenige', 'wettbewerbID' => $turnier['wettbewerbID']], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
    ]
]);
