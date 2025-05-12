<?php
use app\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $spiel \app\models\Spiel */
/** @var $heim bool */

$seite = $heim ? 'heimname' : 'auswaertsname';
$teamName = $heim ? $spiel->club1->name : $spiel->club2->name;
$teamFlagge = $heim ? $spiel->club1->land : $spiel->club2->land;
$tournamentID = $spiel->turnier->tournamentID ?? null;

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$aufstellung = $heim ? $spiel->aufstellung1 : $spiel->aufstellung2;
$type = $heim ? 'H' : 'A';
$clubID = $heim ? $spiel->club1->id : $spiel->club2->id;
$spielID = $spiel->id;
?>
    
<div class="spielinfo-box">
      <h4><?php echo Helper::getFlagInfo($teamFlagge, $spiel->turnier->datum, $teamName, null); ?></h4>


    <div class="highlights-content">

		<input type="hidden" name="spielID" value="<?= $spielID ?>">
		<input type="hidden" name="clubID-<?= $type ?>" value="<?= $clubID ?>">

        <?php foreach (range(1, 11) as $i): ?>
            <?php
            $spielerProperty = "spieler{$i}";
            $spieler = $aufstellung->$spielerProperty ?? null;
            ?>
            <?php if (!Yii::$app->user->isGuest) :?>
	            <div class="form-group mb-2">
            	    <input type="text"
                           class="form-control autocomplete-input"
                           id="spieler<?= $type ?>Text-<?= $i ?>"
                           placeholder="Spieler <?= $i ?>"
                           value="<?= Html::encode($spieler ? $spieler->fullname : '') ?>"
                           data-id-input="spieler-id-<?= $type ?>-<?= $i ?>"
                           data-fetch-type="<?= $type === 'H' ? 'home' : 'away' ?>"
                           data-club-id="<?= $clubID ?>">
                    <input type="hidden"
                           name="spieler<?= $type ?>[spieler<?= $i ?>]"
                           id="spieler-id-<?= $type ?>-<?= $i ?>"
                           value="<?= $spieler?->id ?>">
                    <div class="autocomplete-suggestions" id="spieler<?= $type ?>Text-<?= $i ?>-suggestions"></div>

	            </div>
            <?php else :?>
	            <div class="form-group mb-2" style="text-align: left; padding-left: 15px;">
					<?= Html::a(Html::encode(trim($spieler->vorname . ' ' . $spieler->name)), ['/spieler/view', 'id' => $spieler->id], ['class' => 'text-decoration-none']) ?>
				<?= \app\components\GameHelper::getActionLogos($spieler->id, $spielID, true) ?><br>
	            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    
        <?php if (!Yii::$app->user->isGuest) :?>
	        <div class="form-group mb-3">
                <input type="text"
                       class="form-control autocomplete-input"
                       id="trainer<?= $type ?>Text"
                       placeholder="Trainer"
                       value="<?= Html::encode($aufstellung?->coach?->fullname ?? '') ?>"
                       data-id-input="trainer-id-<?= $type ?>"
                       data-fetch-type="<?= $type === 'H' ? 'home' : 'away' ?>"
                       data-club-id="<?= $clubID ?>">
                <input type="hidden"
                       name="trainer<?= $type ?>"
                       id="trainer-id-<?= $type ?>"
                       value="<?= $aufstellung?->coach?->id ?? '' ?>">
                <div class="autocomplete-suggestions" id="trainer<?= $type ?>Text-suggestions"></div>
            </div>

    	<?php else : ?>
            <div class="form-group mb-3" style="text-align: left;padding: 5px 5px 5px 25px;background-color: #e0e0e0;margin: 0px -10px;">
				<b>Trainer:</b> <?= Html::a(Html::encode(trim($aufstellung->coach->vorname . ' ' . $aufstellung->coach->name)), ['/spieler/view', 'id' => $aufstellung->coach->id], ['class' => 'text-decoration-none']) ?><br>
            </div>
    	<?php endif;?>
    	
    	<?php
        // Nur im Gastbereich anzeigen
        if (Yii::$app->user->isGuest) {
            $wechselAktionen = \app\models\Games::find()
                ->where(['spielID' => $spielID, 'aktion' => 'AUS', 'zusatz' => $type])
                ->all();
        }
        ?>
        
        <?php if (!empty($wechselAktionen)): ?>
             <div class="form-group mb-2" style="text-align: left; padding-left: 15px;">
       		   <b><i class="material-icons" style="font-size: 16px; vertical-align: middle; margin-bottom: 1rem;">swap_horiz</i> Eingewechselt</b>
       		   </div>
	           <?php foreach ($wechselAktionen as $aktion): ?>
	            <div class="form-group mb-2" style="text-align: left; padding-left: 15px;">
	            
                <?php
                    $spieler = \app\models\Spieler::findOne($aktion->spieler2ID);
                    if ($spieler):
                ?>
                    <?= Html::a(Html::encode(trim($spieler->vorname . ' ' . $spieler->name)), ['/spieler/view', 'id' => $spieler->id], ['class' => 'text-decoration-none']) ?>
					<?= \app\components\GameHelper::getActionLogos($spieler->id, $spielID, true) ?><br>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>
		<?php endif; ?>
    	
    </div>
</div>
