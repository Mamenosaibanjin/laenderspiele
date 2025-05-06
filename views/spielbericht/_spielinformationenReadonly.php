<!-- Stadion -->
<?php use app\components\Helper;

if ($spiel->stadium): ?>
    <div class="info-row">
        <i class="material-icons">stadium</i>
        <span>
            <?= Helper::getFlagUrl($spiel->stadium->land) ?>
            <?= Html::encode($spiel->stadium->name) ?> (<?= Html::encode($spiel->stadium->stadt) ?>)
        </span>
    </div>
    <?php if ($spiel->zuschauer): ?>
        <div class="info-row">
        <i class="material-icons">groups</i>
            <span><?= number_format($spiel->zuschauer, 0, ',', '.') ?></span>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Schiedsrichter -->
<?php
$refs = [$spiel->referee1, $spiel->referee2, $spiel->referee3, $spiel->referee4];
$icons = ['sports', 'sports_score', 'sports_score', 'scoreboard'];
foreach ($refs as $index => $ref) {
    if (!$ref) continue;
    echo '<div class="info-row">';
    echo '<i class="material-icons">' . $icons[$index] . '</i>';
    echo '<span>' . Helper::getFlagUrl($ref->nati1) . ' ' . Html::encode($ref->vorname . ' ' . $ref->name) . '</span>';
    echo '</div>';
}
?>
