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

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

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
                $previousScore = [0, 0];
                foreach ($highlightAktionen as $aktion):
                    echo $this->render('_highlightZeile', [
                        'aktion' => $aktion,
                        'spiel' => $spiel,
                        'previousScore' => $previousScore
                    ]);
                    $previousScore = explode(':', $aktion->zusatz);
                endforeach;
                ?>
                <?php else : ?>
                	keine Highlights
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?= $this->render('_spielinformationen', [
        'spiel' => $spiel,
        'heim' => $heim,
    ]) ?>
    
    <!-- Widget Aufstellungen -->
    <form class="aufstellung-form" data-spiel-id="<?= $spiel->id ?>">
        <div class="alert alert-success" style="display:none; font-size: 11px; margin-top: 5px;" role="alert">
        ✅ Änderungen erfolgreich gespeichert.
    	</div>
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
	</form>
	
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

<script>
const urlRefereeSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/referee/search';
const urlSpielerSuche = '/aufstellung/spieler-suche';
const urlStadionSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/stadion/search';
const spielID = 123;   // Serverseitig ersetzen
const clubID = 456;    // Serverseitig ersetzen

function initAutocompleteAll() {
    document.querySelectorAll('.autocomplete-input').forEach(input => {
        const hiddenInput = document.getElementById(input.dataset.idInput);
		const fetchType = input.dataset.fetchType?.trim() || 'referee';
        const suggestionBox = document.getElementById(input.id + '-suggestions');

        let fetchedData = [];

        input.addEventListener('input', async () => {
            const term = input.value.trim();
            if (term.length < 2) {
                suggestionBox.style.display = 'none';
                return;
            }

            let url;
            if (fetchType === 'referee') {
                url = `${urlRefereeSuche}?term=${encodeURIComponent(term)}`;
            } else if (fetchType === 'stadium') {
                url = `${urlStadionSuche}?term=${encodeURIComponent(term)}`;
            } else {
                // Spieler
                url = `${urlSpielerSuche}?spielID=${spielID}&clubID=${clubID}&term=${encodeURIComponent(term)}`;
            }

            try {
                const res = await fetch(url);
                const data = await res.json();
                fetchedData = data;

                suggestionBox.innerHTML = '';
                suggestionBox.style.position = 'absolute';
                suggestionBox.style.zIndex = 999;
                suggestionBox.style.background = '#fff';
                suggestionBox.style.border = '1px solid #ccc';
                suggestionBox.style.width = input.offsetWidth + 'px';

                data.forEach(d => {
                    const div = document.createElement('div');
                    div.textContent = d.value;
                    div.classList.add('suggestion-item');
                    div.style.padding = '4px 8px';
                    div.style.cursor = 'pointer';

                    div.addEventListener('click', () => {
                        input.value = d.value;
                        hiddenInput.value = d.id;
                        suggestionBox.style.display = 'none';
                    });

                    suggestionBox.appendChild(div);
                });

                suggestionBox.style.display = data.length ? 'block' : 'none';
                const rect = input.getBoundingClientRect();
                suggestionBox.style.top = input.offsetTop + input.offsetHeight + 'px';
                suggestionBox.style.left = input.offsetLeft + 'px';
            } catch (err) {
                console.error('Autocomplete Fetch Error', err);
            }
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.style.display = 'none';
            }
        });
    });
}

// Aufrufen nach DOM-Load:
document.addEventListener('DOMContentLoaded', initAutocompleteAll);
</script>