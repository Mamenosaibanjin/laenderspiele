<div class="panel panel-default">
    <div class="panel-heading">Wechsel</div>
    <div class="panel-body">
        <ul>
            <?php use yii\helpers\Html;

foreach ($highlightAktionen as $aktion): ?>
                <?php if ($aktion->aktion === 'AUS'): ?>
                    <li>
                        <?= Html::encode($aktion->minute) ?>' 
                        <?= Html::encode($aktion->spieler->name) ?> 
                        <?= '▶' ?> 
                        <?= Html::encode($aktion->spieler2->name) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
