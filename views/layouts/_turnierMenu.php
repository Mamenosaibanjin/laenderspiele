<?php
use yii\bootstrap5\Nav;

/** @var array $turnier */

echo Nav::widget([
    'options' => ['class' => 'navbar-nav flex-row'],
    'items' => [
        [
            'label' => 'Turnier',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                ['label' => 'Übersicht', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Ergebnisse / Tabelle', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/ergebnisse"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spielplan', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/spielplan"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Teilnehmer', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/teilnehmer"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Spieler', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/spieler"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torjäger', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/torjaeger"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Stadien', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/stadien"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Schiedsrichter', 'url' => ["/turnier/{$turnier['wettbewerbID']}/{$turnier['jahr']}/schiedsrichter"], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
        [
            'label' => 'Statistik',
            'linkOptions' => ['class' => 'btn btn-turnier dropdown-toggle', 'data-bs-toggle' => 'dropdown'],
            'items' => [
                ['label' => 'Archiv', 'url' => ["/turnier/{$turnier['wettbewerbID']}/archiv"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Statistik', 'url' => ["/turnier/{$turnier['wettbewerbID']}/statistik"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Alle Sieger', 'url' => ["/turnier/{$turnier['wettbewerbID']}/alle-sieger"], 'linkOptions' => ['class' => 'dropdown-item']],
                ['label' => 'Torschützenkönige', 'url' => ["/turnier/{$turnier['wettbewerbID']}/torschuetzenkoenige"], 'linkOptions' => ['class' => 'dropdown-item']],
            ],
        ],
    ]
]);
