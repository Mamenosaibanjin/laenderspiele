<?php
use models\Flag; // Falls die Datei Flag.php heißt
use models\Spieler;

it('findet Spieler mit falschem Geburtsland', function () {
    $fehlerhafteSpieler = Spieler::find()
    ->where(['not in', 'geburtsland', Flag::find()->select('key')->column()])
    ->select('id')
    ->column(); // Gibt ein Array mit den IDs zurück
    
    expect($fehlerhafteSpieler)->toBeEmpty("Fehlerhafte Spieler-IDs: " . implode(', ', $fehlerhafteSpieler));
});
    ?>