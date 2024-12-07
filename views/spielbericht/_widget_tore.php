<div class="panel panel-default">
    <div class="panel-heading">Tore</div>
    <div class="panel-body">
        <ul>
            <?php use yii\bootstrap5\Html;

foreach ($highlightAktionen as $aktion): ?>
                <?php if (in_array($aktion->aktion, ['TOR', '11m'])): ?>
                    <li>
                        <?= Html::encode($aktion->minute) ?>' 
                        <?= Html::encode($aktion->spieler->name) ?> 
                        <?= $aktion->aktion === '11m' ? '(11m)' : '' ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
