<?php
use app\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
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
            <div class="highlights-content">
			<?php if (!Yii::$app->user->isGuest): ?>
    <div class="highlight-form-wrapper" style="margin-top: 20px;">
        <?= $this->render('_highlightForm', [
            'spiel' => $spiel,
            'highlights' => $highlights,
        ]) ?>
    </div>
<?php else: ?>
    <!-- Highlight-Widget -->
    <?= $this->render('_highlights', [
        'spiel' => $spiel, 
        'highlights' => $highlightAktionen
    ]) ?>

<?php endif; ?>
                
            </div>
        </div>
    </div>

    <?= $this->render('_spielinformationen', [
        'spiel' => $spiel,
        'heim' => $heim,
    ]) ?>
    
    <br><br>
    <!-- Widget Aufstellungen -->
    <?php $form = ActiveForm::begin([
        'action' => ['aufstellung/speichern'],
        'method' => 'post',
        'options' => ['class' => 'aufstellung-form']
    ]) ?>
    <div class="highlights-box">

    <?php if ($spiel->aufstellung1 || $spiel->aufstellung2 || !Yii::$app->user->isGuest) : ?>
        <div class="panel-body">
            <div style="max-width: 640px; margin: auto; text-align: center;">
                
                <div class="aufstellung-wrapper">
                    <div class="aufstellung-column">
                        <?= \app\components\widgets\AufstellungWidget::widget([
                            'spiel' => $spiel,
                            'heim' => true,
                            'wechsel' => $wechselHeim,
                        ]) ?>
                    </div>
                    <div class="aufstellung-column">
                        <?= \app\components\widgets\AufstellungWidget::widget([
                            'spiel' => $spiel,
                            'heim' => false,
                            'wechsel' => $wechselAuswaerts,
                        ]) ?>
                    </div>
                </div>
                                
                
                <?php if (!Yii::$app->user->isGuest) :?>
    			     <?= Html::submitButton('Aufstellungen speichern', ['class' => 'btn btn-secondary']) ?>
            	<?php endif; ?>
            </div>
        </div>
	    <?php endif; ?>
        </div>
	<?php ActiveForm::end() ?>
	
	<?php if (Yii::$app->user->isGuest): ?>
	
        <!-- Tore-Widget -->
        <?php if ($toreAktionen) :?>
        <?= $this->render('_tore', [
            'spiel' => $spiel, 
            'toreAktionen' => $toreAktionen
        ]) ?>
    	<?php endif;?>
            
        <!-- Besondere Vorkommnisse-Widget -->
        <?php if ($besondereAktionen) :?>
        <?= $this->render('_besondereAktionen', [
            'spiel' => $spiel, 
            'besondereAktionen' => $besondereAktionen
        ]) ?>
    	<?php endif;?>
    
        <!-- Karten-Widget -->
        <?php if ($kartenAktionen) :?>
        <?= $this->render('_karten', [
            'spiel' => $spiel, 
            'kartenAktionen' => $kartenAktionen
        ]) ?>
        <?php endif; ?>
    
    	<!-- Wechsel-Widget -->
    	<?php if ($wechselAktionen) : ?>
        <?= $this->render('_wechsel', [
            'spiel' => $spiel, 
            'wechselAktionen' => $wechselAktionen
        ]) ?>
        <?php endif;?>
    <?php endif;?>
</div>

<script>
const urlRefereeSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/referee/search';
const urlSpielerSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/aufstellung/spieler-suche';
const urlSpielerAufstellungSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/aufstellung/spieler-aufstellung-suche';
const urlStadionSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/stadion/search';
const spielID = <?= (int)$spiel->id ?>;   // Serverseitig ersetzen

function initAutocompleteAll() {
    document.querySelectorAll('.autocomplete-input').forEach(input => {
        const hiddenInput = document.getElementById(input.dataset.idInput);
		const fetchType = input.dataset.fetchType?.trim() || 'referee';
		const clubIDFromInput = input.dataset.clubId;
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
            } else if (fetchType === 'home' || fetchType === 'away') {
                const club = clubIDFromInput || clubID;
                url = `${urlSpielerAufstellungSuche}?spielID=${spielID}&clubID=${club}&term=${encodeURIComponent(term)}`;
            } else {
                // Spieler
                url = `${urlSpielerSuche}?spielID=${spielID}&term=${encodeURIComponent(term)}`;
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
        
        let currentIndex = -1;
        
        input.addEventListener('keydown', (e) => {
            const items = suggestionBox.querySelectorAll('.suggestion-item');
            if (!items.length) return;
        
            if (e.key === 'ArrowDown') {
                currentIndex = (currentIndex + 1) % items.length;
                items.forEach(item => item.classList.remove('active'));
                items[currentIndex].classList.add('active');
                input.value = items[currentIndex].textContent;
                e.preventDefault();
            } else if (e.key === 'ArrowUp') {
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                items.forEach(item => item.classList.remove('active'));
                items[currentIndex].classList.add('active');
                input.value = items[currentIndex].textContent;
                e.preventDefault();
            } else if (e.key === 'Enter') {
                if (currentIndex >= 0) {
                    items[currentIndex].click();
                    e.preventDefault();
                }
            }
		});

    });

}


// Aufrufen nach DOM-Load:
document.addEventListener('DOMContentLoaded', initAutocompleteAll);
</script>