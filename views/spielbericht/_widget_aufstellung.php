<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $spiel \app\models\Spiel */
/** @var $heim bool */

$seite = $heim ? 'heimname' : 'auswaertsname';
$teamName = $heim ? 'Heim' : 'Auswärts';
$icon = $heim ? 'home' : 'flight';
$tournamentID = $spiel->turnier->tournamentID ?? null;

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$aufstellung = $heim ? $spiel->aufstellung1 : $spiel->aufstellung2;
$type = $heim ? 'H' : 'A';
$clubID = $heim ? $spiel->club1->id : $spiel->club2->id;
$spielID = $spiel->id;
?>
    
<div class="spielinfo-box">
      <h4><i class="material-icons"><?= $icon ?></i> <?= $teamName ?></h4>


    <div class="highlights-content">

        <?php foreach (range(1, 11) as $i): ?>
            <?php
            $spielerProperty = "spieler{$i}";
            $spieler = $aufstellung->$spielerProperty ?? null;
            ?>
            <?php if (!Yii::$app->user->isGuest) :?>
	            <div class="form-group mb-2">
            	    <input type="text"
                	       class="form-control awesomplete"
    	              	       placeholder="Spieler <?= $i ?>"
                   	       value="<?= Html::encode($spieler ? $spieler->fullname : '') ?>"
                   	       data-id-field="#spieler-id-<?= $type ?>-<?= $i ?>">
                	<input type="hidden"
                           name="spieler[spieler<?= $i ?>]"
                           id="spieler-id-<?= $type ?>-<?= $i ?>"
                           value="<?= $spieler?->id ?>">
	            </div>
            <?php else :?>
	            <div class="form-group mb-2" style="text-align: left; padding-left: 15px;">
					<?= Html::a(Html::encode(trim($spieler->vorname . ' ' . $spieler->name)), ['/spieler/view', 'id' => $spieler->id], ['class' => 'text-decoration-none']) ?><br>
	            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    
        <?php if (!Yii::$app->user->isGuest) :?>
	        <div class="form-group mb-3">
                <input type="text"
                       class="form-control awesomplete"
                       placeholder="Trainer"
    				   value="<?= Html::encode($aufstellung?->coach?->fullname ?? '') ?>"
                       data-id-field="#trainer-id-<?= $type ?>">
                <input type="hidden"
                       name="trainer"
                       id="trainer-id-<?= $type ?>"
                       value="<?= $aufstellung?->coach?->id ?? '' ?>">
            </div>
    	<?php else : ?>
            <div class="form-group mb-3" style="text-align: left;padding: 5px 5px 5px 25px;background-color: #e0e0e0;margin: 0px -10px;">
				<b>Trainer:</b> <?= Html::a(Html::encode(trim($aufstellung->coach->vorname . ' ' . $aufstellung->coach->name)), ['/spieler/view', 'id' => $aufstellung->coach->id], ['class' => 'text-decoration-none']) ?><br>
            </div>
    	<?php endif;?>
    </div>
</div>
