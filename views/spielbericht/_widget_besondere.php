<div class="panel panel-default">
    <div class="panel-heading">Besondere Vorkommnisse</div>
    <div class="panel-body">
        <ul>
            <?php use yii\helpers\Html;

foreach ($highlightAktionen as $aktion): ?>
                <?php if ($aktion->aktion === '11mX'): ?>
                    <li>
                        <?= Html::encode($aktion->minute) ?>' 
                        <?php if ($aktion->zusatz === 'h'): ?>
                            <?= Html::encode($aktion->spieler2->name) ?> hält den Elfmeter
                        <?php elseif ($aktion->zusatz === 'v'): ?>
                            <?= Html::encode($aktion->spieler->name) ?> verschießt den Elfmeter
                        <?php elseif ($aktion->zusatz === 'p'): ?>
                            <?= Html::encode($aktion->spieler->name) ?> schießt den Elfmeter an den Pfosten
                        <?php elseif ($aktion->zusatz === 'l'): ?>
                            <?= Html::encode($aktion->spieler->name) ?> schießt den Elfmeter an die Latte
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
