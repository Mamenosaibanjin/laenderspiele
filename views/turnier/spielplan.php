<?php
use app\components\Helper;
use app\models\Runde;
use app\models\Spiel;
use app\models\Tournament;
use app\models\Turnier;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TabellenHelper;

$this->registerCssFile('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
$this->registerJsFile('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

/** @var $turnier app\models\Turnier */
/** @var $runden app\models\Runde[] */
/** @var $runde app\models\Runde */
/** @var $spiele app\models\Spiel[] */

$this->title = "Spielplan – {$turnier->jahr} ({$turnier->wettbewerb->name})";
$isEditing = !(Yii::$app->user->isGuest); // Zustand für Bearbeitungsmodus

?>


<!-- Runden-Dropdown -->
<div class="d-flex">
    <?php 
    $aktuellesTurnier = Tournament::findOne($turnier->id);
    $wettbewerbID = $aktuellesTurnier->wettbewerbID;

    $verwandteTurniere = Tournament::find()
        ->where(['wettbewerbID' => $wettbewerbID])
        ->orderBy(['jahr' => SORT_ASC])
        ->all();

    $seite = Yii::$app->request->get('seite') ?? 'spielplan';
    
    // Nav mit beiden Dropdowns
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav flex-row'],
        'items' => [

            // Turnier-Dropdown
            [
                'label' => Helper::getTurniernameFullnameForDropdown($turnier->id),
                'linkOptions' => ['class' => 'btn btn-wettbewerbe'],
                'items' => array_map(function ($t) use ($turnier, $seite) {
                    return [
                        'label' => Helper::getTurniernameFullnameForDropdown($t->id),
                        'url' => ['/turnier/' . $t->id . '/' . $seite],
                        'active' => $t->id == $turnier->id,
                    ];
                }, $verwandteTurniere),
                'dropdownOptions' => ['class' => 'scrollable-dropdown'],
            ],

        ],
    ]);
    ?>
</div>

<style>
/* Custom Scrollable Dropdown Style */
.scrollable-dropdown {
    max-height: 200px; /* ca. 5 Einträge */
    overflow-y: auto;
}
</style>

<div class="container mt-3">

<?php 
// Flaggenanzeige vorbereiten
$laenderKeys = !empty($turnier->land) ? explode('/', $turnier->land) : [];
$flaggen = '';
foreach ($laenderKeys as $key) {
    $startdatum = $turnier->startdatum ? substr($turnier->startdatum, 0, 4) . '-' . substr($turnier->startdatum, 4, 2) . '-01' : null;
    $flaggen .= Helper::getFlagInfo($key, $startdatum, false);
}
?>

<?php if ($isEditing): ?>
<div class="filter-box-spieler mb-3">
    <h5>
        <span class="material-icons align-middle me-1">sports_soccer</span>
        Neues Spiel anlegen
    </h5>

    <?php $form = ActiveForm::begin([
        'action' => ['turnier/anlegen', 'turnierID' => $turnier->id],
        'method' => 'post'
    ]); ?>

    <div class="row">
        <!-- Club 1 -->
        <div class="col-md-4">
            <label for="club1Text" class="form-label">
                <span class="material-icons align-middle me-1">home</span>
                Club 1
            </label>
            <input type="text" class="autocomplete-input form-control"
                   id="club1Text"
                   data-id-input="hidden-club1-id"
                   data-fetch-type="club"
                   placeholder="Club 1 suchen">
            <input type="hidden" id="hidden-club1-id" name="club1ID" value="">
            <div class="autocomplete-suggestions" id="club1Text-suggestions"></div>
        </div>

        <!-- Club 2 -->
        <div class="col-md-4">
            <label for="club2Text" class="form-label">
                <span class="material-icons align-middle me-1">groups</span>
                Club 2
            </label>
            <input type="text" class="autocomplete-input form-control"
                   id="club2Text"
                   data-id-input="hidden-club2-id"
                   data-fetch-type="club"
                   placeholder="Club 2 suchen">
            <input type="hidden" id="hidden-club2-id" name="club2ID" value="">
            <div class="autocomplete-suggestions" id="club2Text-suggestions"></div>
        </div>

        <!-- Turnier -->
        <div class="col-md-4">
            <label for="tournamentText" class="form-label">
                <span class="material-icons align-middle me-1">emoji_events</span>
                Turnier
            </label>
            <input type="text" class="autocomplete-input form-control"
                   id="tournamentText"
                   data-id-input="hidden-tournament-id"
                   data-fetch-type="tournament"
                   placeholder="Turnier suchen">
            <input type="hidden" id="hidden-tournament-id" name="tournamentID" value="">
            <div class="autocomplete-suggestions" id="tournamentText-suggestions"></div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <?= Html::label('<span class="material-icons align-middle me-1">event</span> Datum', 'datum', ['class' => 'form-label']) ?>
            <?= Html::input('date', 'datum', '', ['class' => 'form-control']) ?>
        </div>

        <div class="col-md-4">
            <?= Html::label('<span class="material-icons align-middle me-1">schedule</span> Zeit', 'zeit', ['class' => 'form-label']) ?>
            <?= Html::input('time', 'zeit', '', ['class' => 'form-control']) ?>
        </div>

        <div class="col-md-4">
            <?= Html::label('<span class="material-icons align-middle me-1">flag</span> Runde', 'rundeID', ['class' => 'form-label']) ?>
            <?= Html::dropDownList("rundeID", null, \yii\helpers\ArrayHelper::map(Runde::find()->all(), 'id', 'name'), [
                'class' => 'form-control',
                'id' => 'rundeID',
                'prompt' => 'Runde wählen',
            ]) ?>
        </div>

        <div class="row mt-2">
            <!-- Eingabefeld für Spieltag (versteckt, bis nötig) -->
            <div class="col-md-3 d-none" id="spieltag-container">
                <?= Html::label('<span class="material-icons align-middle me-1">looks_one</span> Spieltag', 'spieltag', ['class' => 'form-label']) ?>
                <?= Html::input('number', 'spieltag', 1, [
                    'class' => 'form-control',
                    'id' => 'spieltag',
                    'min' => 1,
                ]) ?>
    		</div>
		</div>
    		
        <div class="row mt-2">
            <div class="col-md-3 d-flex align-items-end">
                <?= Html::submitButton('Spiel hinzufügen', [
                    'class' => 'btn btn-primary w-100'
                ]) ?>
            </div>
        </div>
    		

    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php endif; ?>

    <!-- Widget 1: Ergebnisse -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0"><?= $flaggen ?> <?= Html::encode($turnier->wettbewerb->name) ?> <?= Html::encode($turnier->jahr) ?></h3>
        </div>
        <div class="card-body">

            <!-- Normale Tabelle ohne Eingabe -->
            <table class="table table-striped">
                <tbody>
                	<?php $aktuelleRunde = '';?>
                	<?php $aktuellesDatum = '';?>
                    <?php foreach ($spiele as $spiel): ?>
                    	<?php if ($aktuelleRunde != $spiel->runde->name) :?>
                    		<tr>
                    			<td colspan="6" class="header_green">
                    				<?= Html::a($spiel->runde->name, ['turnier/ergebnisse', 'tournamentID' => $turnier->id, 'rundeID' => $spiel->runde->id], ['class' => 'text-decoration-none']); ?>
                    			</td>
                    		</tr>
                  		<?php endif;?>
                        <tr>
                            <td>
                            	<?php if ($aktuellesDatum != $spiel->datum) : ?>
                            		<?= Yii::$app->formatter->asDate($spiel->datum) ?>
                            	<?php endif;?>
                            </td>
                            <td><?= Yii::$app->formatter->asTime($spiel->zeit, 'short') ?></td>
                            <td align="right">
                                <?php
                                    $club1Name = Html::a($spiel->spiel->club1->name, ['club/view', 'id' => $spiel->spiel->club1ID], ['class' => 'text-decoration-none']);
                                    $club2Name = Html::a($spiel->spiel->club2->name, ['club/view', 'id' => $spiel->spiel->club2ID], ['class' => 'text-decoration-none']);
                                ?>
                                <?= $club1Name  . " " . Helper::getFlagInfo(Helper::getClubNation($spiel->club1->id), $turnier->startdatum, false) ?>
                            </td>
                            <td align="center">–</td>
                            <td><?= Helper::getFlagInfo(Helper::getClubNation($spiel->club2->id), $turnier->startdatum, false) . " " .$club2Name ?></td>

                            <td align="center"><?= Html::a($spiel->getErgebnisHtml(), ['/spielbericht/view', 'id' => $spiel->spiel->id], ['class' => 'text-decoration-none']) ?>
                        </tr>
                        <?php $aktuelleRunde = $spiel->runde->name; ?>
                        <?php $aktuellesDatum = $spiel->datum; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        </div>
    </div>

</div>

<?php
$spielID = isset($spiel->id) ? (int)$spiel->id : 0;
?>
<script>
const urlClubSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/club/search';
const urlTournamentSuche = '/projects/laenderspiele2.0/yii2-app-basic/web/turnier/search';
const spielID = <?= $spielID ?>;

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
			if (fetchType === 'club') {
                url = `${urlClubSuche}?term=${encodeURIComponent(term)}`;
			} else if (fetchType === 'tournament') {
                url = `${urlTournamentSuche}?term=${encodeURIComponent(term)}`;
            } else {
                url = `${urlClubSuche}?term=${encodeURIComponent(term)}`;
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

document.addEventListener('DOMContentLoaded', initAutocompleteAll);
</script>
<?php
$js = <<<JS
$('#rundeID').on('change', function() {
    var selectedText = $("#rundeID option:selected").text();
    if (selectedText.startsWith('Gruppe')) {
        $('#spieltag-container').removeClass('d-none');
    } else {
        $('#spieltag-container').addClass('d-none');
        $('#spieltag').val(0); // sicherheitshalber zurücksetzen
    }
});
JS;
$this->registerJs($js);
?>