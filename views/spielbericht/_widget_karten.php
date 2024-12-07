<div class="panel panel-default">
    <div class="panel-heading">Karten</div>
    <div class="panel-body">
        <ul>
            <?php use yii\helpers\Html;

foreach ($highlightAktionen as $aktion): ?>
                <?php if (in_array($aktion->aktion, ['GK', 'GRK', 'RK'])): ?>
                    <li>
                        <?= Html::encode($aktion->minute) ?>' 
                        <?= Html::encode($aktion->spieler->name) ?> 
                        <?= $aktion->aktion === 'GK' ? '[Gelbe Karte]' : '' ?>
                        <?= $aktion->aktion === 'GRK' ? '[Gelb-Rot]' : '' ?>
                        <?= $aktion->aktion === 'RK' ? '[Rote Karte]' : '' ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
