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
                ['label' => 'Ergebnisse / Tabelle', 'url' => ['turnier/ergebnisse', 'tournamentID' => $turnier->id, 'rundeID' => '8'], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spielplan', 'url' => ['turnier/spielplan', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Teilnehmer', 'url' => ['turnier/teilnehmer', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spieler', 'url' => ['turnier/spieler', 'tournamentID' => $turnier->id, 'positionen' => '1,2,3,4,5,6,7', 'sort' => 'nach-name', 'page' => 1], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torjäger', 'url' => ['turnier/torjaeger', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Stadien', 'url' => ['turnier/stadien', 'tournamentID' => $turnier->id, 'sort' => 'nach-name', 'page' => 1], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Schiedsrichter', 'url' => ['turnier/schiedsrichter', 'tournamentID' => $turnier->id, 'sort' => 'nach-name', 'page' => 1], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
        [
            'label' => 'Statistik',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                '<h6 class="dropdown-header">Archiv</h6>',
                ['label' => 'Übersicht', 'url' => ['turnier/archiv', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Alle Sieger', 'url' => ['turnier/alle-sieger', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torschützenkönige', 'url' => ['turnier/alle-torjaeger', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                '<h6 class="dropdown-header">Statistik</h6>',
                ['label' => 'Tore pro Saison', 'url' => ['turnier/tore-pro-saison', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Tore pro Spielrunde', 'url' => ['turnier/tore-pro-runde', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Die höchsten Siege', 'url' => ['turnier/hoechste-siege', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Die torreichsten Spiele', 'url' => ['turnier/torreichste-spiele', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Die meisten Tore eines Spielers', 'url' => ['turnier/meiste-tore-eines-spielers', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Die unfairsten Spiele', 'url' => ['turnier/unfairste-spiele', 'tournamentID' => $turnier->id], 'linkOptions' => ['class' => 'dropdown-item']],
                
            ],
        ],
    ]
]);
