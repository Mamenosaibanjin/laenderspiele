<?php
use app\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Spielbericht;

/* @var $this yii\web\View */
/* @var $spiel app\models\Spiel */
/* @var $highlightAktionen app\models\Games[] */
/* @var $aufstellung1 app\models\Aufstellung */
/* @var $aufstellung2 app\models\Aufstellung */

$this->title = 'Spielbericht: ' . Html::encode($spiel->heimClub->name) . ' vs ' . Html::encode($spiel->auswaertsClub->name);
$heim = $heim ?? true;
$term = $term ?? '';
?>

<?php
$verlaengerung = '';
if ($spiel->extratime) {
    $verlaengerung = '<div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">n.V.</div>';
} elseif ($spiel->penalty) {
    $verlaengerung = '<div style="padding-left: 20px; font-size: 20px; margin-top: 20px;">i.E.</div>';
} else {
    $verlaengerung = '';
}
?>

<div class="card" style="padding-bottom: 25px;">
	<div class="card-header">
		<h3>Spielbericht <?= Html::encode($spiel->heimClub->name) ?> - <?= Html::encode($spiel->auswaertsClub->name) ?>
    		<?= '('. Html::encode($spiel->turnier->getErgebnis()) . ')'?>
            <?= $verlaengerung; ?>
		</h3>
	</div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4 text-center">
                <?= Html::img(Helper::getClubLogoUrl($spiel->heimClub->id), ['alt' => $spiel->heimClub->name, 'class' => 'team-logo', 'style' => 'height: 100px; padding: 10px;']) ?>
                <div><?= Html::encode($spiel->heimClub->name) ?></div>
            </div>
            <?php //echo "<pre>";var_dump($spiel);echo "</pre>";exit;?>
            <div class="col-sm-4 digital-scoreboard" style="font-size: 50px;">
				<?= $spiel->turnier ? Html::encode($spiel->turnier->getErgebnis()) : 'Kein Ergebnis verfügbar' ?>
                <?= $verlaengerung; ?>
            </div>
            <div class="col-sm-4 text-center">
                <?= Html::img(Helper::getClubLogoUrl($spiel->auswaertsClub->id), ['alt' => $spiel->auswaertsClub->name, 'class' => 'team-logo', 'style' => 'height: 100px; padding: 10px;']) ?>
                <div><?= Html::encode($spiel->auswaertsClub->name) ?></div>
            </div>
        </div>
    </div>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Highlights
                </span>
            </div>
            <div class="highlights-content">
			<?php if ($highlightAktionen) : ?>
            <?php
            $previousScore = [0, 0]; // [Heimtore, Auswärtstore]
            
            foreach ($highlightAktionen as $aktion): 
                $minute = $aktion->minute;
                $zusatz = $aktion->zusatz; // Ergebnis, z.B. "3:0"
                $currentScore = explode(':', $zusatz);
                $spielerName = ' ';
                $team = ' ';
            
                if (!$aktion->spieler) {
                    // Unbekannter Spieler
                    if ($aktion->aktion === 'ET') {
                        // Eigentor logik
                        if ($currentScore[0] > $previousScore[0]) {
                            $team = 'auswärts'; // Eigentor Heimteam
                        } else {
                            $team = 'heim'; // Eigentor Auswärtsteam
                        }
                    } else {
                        // Normales Tor (kein Schütze bekannt)
                        if ($currentScore[0] > $previousScore[0]) {
                            $team = 'heim';
                        } elseif ($currentScore[1] > $previousScore[1]) {
                            $team = 'auswärts';
                        }
                    }
                    $spielerName = 'unbekannt';
                } else {
                    // Spieler ist bekannt
                    $team = $spiel->isAuswaertsAktion($aktion->spieler->id) ? 'auswärts' : 'heim';
                    $spielerName = Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name);
                }
            
                $actionSvg = Helper::getActionSvg($aktion->aktion);
            ?>
				<?php $relevanteAktionen = ['TOR', '11m', 'ET', 'RK'];
                    
				    if (in_array($aktion->aktion, $relevanteAktionen)):?>
                    <div class="highlight-row">
                        <div class="heimname">
                            <?= ($team === 'heim') ? $spielerName : ' ' ?>
                        </div>
                        <div class="heim">
                            <?= ($team === 'heim' && ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET' || $aktion->aktion == 'RK')) ? Html::encode($zusatz) : ' ' ?>
                        </div>
                        <div class="heim">
                            <?= ($team === 'heim') ? $actionSvg : ' ' ?>
                        </div>
                        <div class="minute">
                            <?= Html::encode($minute) < 200 ? Html::encode($minute) . '.' : ' ' ?>
                        </div>
                        <div class="auswaerts">
                            <?= ($team === 'auswärts') ? $actionSvg : ' ' ?>
                        </div>
                        <div class="auswaerts">
                            <?= ($team === 'auswärts' && ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET' || $aktion->aktion == 'RK')) ? Html::encode($zusatz) : ' ' ?>
                        </div>
                        <div class="auswaertsname">
                            <?= ($team === 'auswärts') ? $spielerName : ' ' ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                    $previousScore = $currentScore; // Ergebnis aktualisieren
                endforeach; 
                ?>
                <?php else : ?>
                	keine Highlights
                <?php endif; ?>
            </div>
        </div>
    </div>

	<!-- Widget Spielinformationen -->
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Spielinformationen
                </span>
            </div>
            <div class="highlights-content heimname" style="width: 100% !important; text-align: left;">
				<div class="spiel-info" style="text-align: left;">

                <!-- Turniername -->
                <?php if ($spiel->turnier->tournament): ?>
                    <div class="info-row">
                    	<i class="material-icons">emoji_events</i>
                        <span>
                            <?= Helper::getTurniernameFullname($spiel->turnier->tournament->id, $spiel->turnier->jahr) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Datum und Zeit -->
                <?php if ($spiel->turnier->datum): ?>
                    <div class="info-row">
	                    <i class="material-icons">calendar_month</i>
                        <span>
                            <?= Yii::$app->formatter->asDate($spiel->turnier->datum, 'php:d.m.Y') ?>
							<?php if ($spiel->turnier->zeit): ?> - <?= Yii::$app->formatter->asTime($spiel->turnier->zeit, 'php:H:i') ?>
							<?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>

				<?php if (!Yii::$app->user->isGuest) : ?>
					<?php
                        $stadien = \app\models\Stadion::find()->all();
                        $referees = \app\models\Referee::find()->all();
                        
                        $stadiumList = implode(', ', array_map(function($s) {
                            return Html::encode("{$s->name} ({$s->stadt})") . ' (' . $s->land . ')';
                        }, $stadien));
                        
                        $refereeList = implode(', ', array_map(function($r) {
                            return Html::encode("{$r->vorname} {$r->name}") . ' (' . $r->nati1 . ')';
                        }, $referees));
                        ?>
					
					<!-- Stadion -->
                    <div class="info-row">
        				<i class="material-icons">stadium</i>
        				<input type="text"
                               class="form-control awesomplete"
                               style="margin-bottom: 5px; font-size: 11px;"
                               placeholder="Stadion"
                               value="<?= Html::encode($spiel->stadium?->name ?? '') ?>"
                               data-id-field="#stadium-id"
                               data-list="<?= $stadiumList ?>">
                        <input type="hidden" name="stadiumID" id="stadium-id" value="<?= $spiel->stadiumID ?>">
					</div>
                    <div class="info-row">
                        <button type="button" class="btn btn-primary mt-2" style="margin-bottom: 5px; font-size: 11px; width: 154px; margin-left: 32px;" id="btn-spieler-bearbeiten" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/stadion/new', '_blank')">
                            Neues Stadion
                        </button>
                    </div>
					                    
                    <!-- Zuschauer -->
                    <div class="info-row">
                    	<i class="material-icons">groups</i>
                    	<input type="number" class="form-control awesomplete" style="margin-bottom: 5px; font-size: 11px;" name="zuschauer" value="<?= $spiel->zuschauer ?>" placeholder="Zuschauer">
                    </div>
                    
                    <!-- Schiedsrichter -->
                    <?php $icons = ['sports', 'sports_score', 'sports_score', 'scoreboard'];?>
                    <?php foreach ([1, 2, 3, 4] as $i): 
                        $ref = $spiel["referee{$i}"]
                    ?>
                        <div class="info-row">
                            <i class="material-icons"><?= $icons[$i - 1] ?></i>
                                <input type="text"
                                       class="form-control awesomplete"
		                               style="margin-bottom: 5px; font-size: 11px;"
                                       placeholder="Schiedsrichter <?= $i ?>"
                                       value="<?= Html::encode($ref ? "{$ref->vorname} {$ref->name}" : '') ?>"
                                       data-id-field="#referee-id-<?= $i ?>"
                                       data-list="<?= $refereeList ?>">
                                <input type="hidden" name="referee<?= $i ?>ID" id="referee-id-<?= $i ?>" value="<?= $ref?->id ?>">
                        </div>
                        
                    <?php endforeach; ?>
                    <div class="info-row">
                        <button type="button" class="btn btn-primary mt-2" style="margin-bottom: 5px; font-size: 11px; width: 154px; margin-left: 32px;" id="btn-spieler-bearbeiten" onclick="window.open('http://localhost/projects/laenderspiele2.0/yii2-app-basic/web/referee/new', '_blank')">
                            Neuer Schiedsrichter
                        </button>
                    </div>
                                        
				<?php else: ?>

                    <!-- Stadion -->
                    <?php if ($spiel->stadium): ?>
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
                    
                    <?php endif; ?>
                    
            	</div>            
            </div>
        </div>
    </div>
    
    <!-- Widget Aufstellungen -->
    <?php if ($spiel->aufstellung1 || $spiel->aufstellung2) : ?>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div style="max-width: 640px; margin: auto; text-align: center;">
            <div style="float: left; width: 45%;">
            
            	<?=  \app\components\widgets\AufstellungWidget::widget([
				'spiel' => $spiel,
				'heim' => true,
				'wechsel' => $wechselHeim,
				]) ?>
        	
        	</div><div style="float: left; width: 10%;">&nbsp;
        	
        	</div><div style="float: left; width: 45%;">
            
            	<?=  \app\components\widgets\AufstellungWidget::widget([
                    'spiel' => $spiel,
                    'heim' => false,
                    'wechsel' => $wechselAuswaerts,
                ]) ?>
  			
  			</div>               
            
            <?php if (!Yii::$app->user->isGuest) :?>
            	<button type="button" class="btn btn-secondary aufstellung-speichern" style="margin-top: 20px;">
            		Aufstellungen speichern
        		</button>
        	<?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tore-Widget -->
    <?php if ($toreAktionen) :?>
    <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Tore
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($toreAktionen as $aktion): ?>
    			<?php if ($aktion->aktion == 'TOR' || $aktion->aktion == '11m' || $aktion->aktion == 'ET') :?>
                    <div class="highlight-row" <?= Html::encode($aktion->minute) == 201 ? 'style="border-top: 1px dashed black; font-size: 12px; font-weight: bolder;"' : ' ' ?>>
                    	<?= Html::encode($aktion->minute) == 201 ? 'Elfmeterschießen</div><div class="highlight-row">' : ' ' ?>
                        <div class="minute" style="width: 10%;">
                        	<?= Html::encode($aktion->minute) < 200 ? Html::encode($aktion->minute) : ' ' ?>
                        </div>
    	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
    	                <div class="auswaerts" style="width: 10%;"><?= Html::encode($aktion->zusatz); ?></div>
    	                <div class="auswaertsname" style="width: 70% !important;"><?= ($aktion->spieler ? Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name) : 'unbekannt')?>
    	                <?php if ($aktion->aktion == '11mX') :?>
    	                	verschießt
    	                <?php endif; ?>
    	                </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            </div>
        </div>
    </div>
    <?php endif; ?>
        
    <!-- Besondere Vorkommnisse-Widget -->
    <?php if ($besondereAktionen) :?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Besondere Vorkommnisse
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($besondereAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 80% !important;">
	                <?php if ($aktion->zusatz == 'v') :?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> verschießt Elfmeter
	         		<?php elseif ($aktion->zusatz == 'p') : ?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> schießt Elfmeter an den Pfosten
	         		<?php elseif ($aktion->zusatz == 'l') : ?>
	         			<?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?> schießt Elfmeter an die Latte
                    <?php elseif ($aktion->zusatz == 'h') : ?>
                        <?php 
                            $gegnerTorhueterID = $spiel->getGegnerTorhueter($aktion->spieler->id); 
                            $gegnerTorhueter = $gegnerTorhueterID ? $spiel->getSpieler($gegnerTorhueterID) : null;
                        ?>
                        <?php if ($gegnerTorhueter) : ?>
                            <?= Html::encode(($gegnerTorhueter->vorname ? mb_substr($gegnerTorhueter->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $gegnerTorhueter->name) ?> hält Elfmeter von <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name) ?>
                        <?php else : ?>
                            Torhüter der gegnerischen Mannschaft hält Elfmeter von <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' ' . $aktion->spieler->name) ?>
                        <?php endif; ?>
                    <?php endif; ?>
	                </div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Karten-Widget -->
    <?php if ($kartenAktionen) :?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Karten
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($kartenAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><?= Helper::getActionSvg($aktion->aktion); ?></div>
	                <div class="auswaertsname" style="width: 80% !important;"><?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?></div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
    <?php endif; ?>

	<!-- Wechsel-Widget -->
	<?php if ($wechselAktionen) : ?>
   <div class="panel-body" style="padding: 25px 25px 0 25px;">
        <div class="highlights-box">
        	<div style="margin-top: -23px;">
                <span class="highlights-header">
                    Wechsel
                </span>
            </div>
            <div class="highlights-content">
			<?php foreach ($wechselAktionen as $aktion): ?>
                <div class="highlight-row">
                    <div class="minute" style="width: 10%;"><?= Html::encode($aktion->minute)?></div>
	                <div class="auswaerts" style="width: 10%;"><i class="fa fa-exchange" aria-hidden="true"></i></div>
	                <div class="auswaertsname" style="width: 80% !important; line-height: 19px;">
	                <?php if ($aktion->spieler) : ?>
	                <?= Html::encode(($aktion->spieler->vorname ? mb_substr($aktion->spieler->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler->name)?><br>
	                <?php else : ?>
	                	unbekannt<br>
	               	<?php endif; ?>
	                <?php if ($aktion->spieler2) : ?>
	                	<?= Html::encode(($aktion->spieler2->vorname ? mb_substr($aktion->spieler2->vorname, 0, 1, 'UTF-8') . '.' : '') . ' '  . $aktion->spieler2->name)?>
	                <?php else : ?>
	                	unbekannt
	                <?php endif; ?>
	                
   	                </div>
                </div>
            <?php endforeach; ?>                
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$spielID = (int)$spiel->id;
$clubID = $heim ? (int)$spiel->club1->id : (int)$spiel->club2->id;
$urlTemplate = Url::to(['aufstellung/spieler-suche'], true);
$urlTemplateSave = Url::to(['aufstellung/speichern'], true);

$js = <<<JS
document.querySelectorAll('.awesomplete').forEach(function(input) {
    const idFieldSelector = input.dataset.idField;
    let awesomplete = new Awesomplete(input, {
        minChars: 2,
        autoFirst: true
    });
    let playerData = [];
    
    input.addEventListener('input', function() {
        const form = input.closest('form');
        const spielID = form.dataset.spielId;
        const clubID = form.dataset.clubId;
        const term = encodeURIComponent(input.value);
        
        const fetchUrl = "{$urlTemplate}" + "?spielID=" + spielID + "&clubID=" + clubID + "&term=" + term;
        
        fetch(fetchUrl)
            .then(res => res.json())
            .then(data => {
                playerData = data;
                awesomplete.list = data.map(d => d.name);
            });
    });
    
    input.addEventListener("awesomplete-selectcomplete", function(evt) {
        const player = playerData.find(p => p.name === evt.text.value);
        if (player) {
            document.querySelector(idFieldSelector).value = player.id;
        }
    });
});
document.querySelectorAll('.aufstellung-speichern').forEach(function(button) {
    button.addEventListener('click', function() {
        const form = button.closest('form');
        const formData = new FormData(form);
        const spieler = {};
        const trainerID = formData.get('trainer') || null;
        const type = form.dataset.type;
        const spielID = form.dataset.spielId;

        for (let pair of formData.entries()) {
            if (pair[0].startsWith('spieler[')) {
                const key = pair[0].match(/spieler\[(.*?)\]/)[1];
                spieler[key] = pair[1];
            }
        }

        fetch('{$urlTemplateSave}' , {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                spielID: spielID,
                type: type,
                spieler: spieler,
                trainer: trainerID,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Aufstellung erfolgreich gespeichert.');
            } else {
                alert('Fehler beim Speichern: ' + JSON.stringify(data.errors));
            }
        })
        .catch(err => {
            alert('Technischer Fehler: ' + err);
        });
    });
});

JS;

$this->registerJs($js);

?>
