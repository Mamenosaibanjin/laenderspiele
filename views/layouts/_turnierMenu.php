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
                ['label' => 'Übersicht', 'url' => ['turnier/index', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Ergebnisse / Tabelle', 'url' => ['turnier/ergebnisse', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spielplan', 'url' => ['turnier/spielplan', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Teilnehmer', 'url' => ['turnier/teilnehmer', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spieler', 'url' => ['turnier/spieler', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torjäger', 'url' => ['turnier/torjaeger', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Stadien', 'url' => ['turnier/stadien', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Schiedsrichter', 'url' => ['turnier/schiedsrichter', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
        [
            'label' => 'Statistik',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                ['label' => 'Archiv', 'url' => ['turnier/archiv', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Statistik', 'url' => ['turnier/statistik', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Alle Sieger', 'url' => ['turnier/alle-sieger', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torschützenkönige', 'url' => ['turnier/torschuetzenkoenige', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
    ]
]);
