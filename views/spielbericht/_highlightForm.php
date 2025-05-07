<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'action' => ['spielbericht/speichern-highlight'],
    'method' => 'post',
    'options' => ['class' => 'highlight-form']
]) ?>

<?php if (!empty($highlights) OR 1==1): ?>
    <div class="spielinfo-box">
        <h4><i class="material-icons">event</i> Highlights</h4>
        <ul class="highlight-list">
            <?php foreach ($highlights as $h): ?>
                <li class="highlight-entry">
                    <strong><?= Html::encode($h->minute) ?>'</strong> 
                    <?= Html::encode($h->aktion) ?>
                    <?php if ($h->spieler): ?>
                        – <?= Html::encode($h->spieler->name) ?>
                    <?php endif; ?>
                    <?php if ($h->spieler2): ?>
                        → <?= Html::encode($h->spieler2->name) ?>
                    <?php endif; ?>
                    <?php if ($h->zusatz): ?>
                        (<?= Html::encode($h->zusatz) ?>)
                    <?php endif; ?>
                    <?= Html::a('<i class="material-icons" style="font-size: 18px; color: red;">delete</i>', 
                        ['spielbericht/delete-highlight', 'id' => $h->id], [
                        'data-confirm' => 'Highlight wirklich löschen?',
                        'data-method' => 'post',
                        'style' => 'margin-left: 8px;'
                    ]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="spielinfo-box">
    <h4><i class="material-icons">event</i> Highlight erfassen</h4>

    <?= Html::hiddenInput('spielID', $spiel->id) ?>

    <div class="info-row">
        <i class="material-icons">schedule</i>
        <?= Html::input('number', 'minute', null, [
            'class' => 'form-control',
            'min' => 1,
            'placeholder' => 'z.B. 45 oder 202 für Elfmeterschießen'
        ]) ?>
    </div>

    <div class="info-row">
        <i class="material-icons">flag</i>
        <select name="aktion" class="form-control" id="aktion-select">
            <option value="">--- bitte wählen ---</option>
            <option value="TOR">Tor</option>
            <option value="11m">Elfmetertor</option>
            <option value="11mX">Verschossener Elfmeter</option>
            <option value="AUS">Auswechslung</option>
            <option value="ET">Eigentor</option>
            <option value="GK">Gelbe Karte</option>
            <option value="GRK">Gelb-Rote Karte</option>
            <option value="RK">Rote Karte</option>
        </select>
    </div>

    <div class="info-row">
        <i class="material-icons">person</i>
        <input type="text" class="autocomplete-input form-control" id="spielerText"
               data-id-input="spielerID"
               data-fetch-type="spieler"
               placeholder="Spieler wählen">
        <input type="hidden" name="spielerID" id="spielerID">
        <div class="autocomplete-suggestions" id="spielerText-suggestions"></div>
    </div>

    <div class="info-row" id="zusatz-wrapper-group" style="display: none;">
        <i class="material-icons">info</i>
        <div id="zusatz-wrapper" style="width: 100%;"></div>
    </div>

    <div class="info-row" id="spieler2-wrapper" style="display: none;">
        <i class="material-icons" id="spieler2-icon">person_add</i>
        <label for="spieler2Text" id="spieler2Label">Spieler 2 wählen</label>
        <input type="text" class="autocomplete-input form-control" id="spieler2Text"
               data-id-input="spieler2ID"
               data-fetch-type="spieler"
               placeholder="Spieler 2 wählen">
        <input type="hidden" name="spieler2ID" id="spieler2ID">
        <div class="autocomplete-suggestions" id="spieler2Text-suggestions"></div>
    </div>

    <div class="info-row">
        <?= Html::submitButton('Highlight speichern', ['class' => 'btn btn-secondary']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>


<script>
const aktionDropdown = document.getElementById('aktion-select');
const zusatzWrapper = document.getElementById('zusatz-wrapper');
const zusatzWrapperGroup = document.getElementById('zusatz-wrapper-group');

const spieler2Wrapper = document.getElementById('spieler2-wrapper');
const spieler2Label = document.getElementById('spieler2Label');
const spieler2Icon = document.getElementById('spieler2-icon');

const zusatzOptions = {
    '11mX': {
        'p': 'Pfosten',
        'v': 'Vorbei',
        'l': 'Latte',
        'h': 'Gehalten'
    },
    'AUS': {
        'H': 'Heimteam',
        'A': 'Auswärtsteam'
    },
    'TOR': 'resultat',
    '11m': 'resultat',
    'ET': 'resultat'
};

function createDropdown(options) {
    const select = document.createElement('select');
    select.name = 'zusatz';
    select.className = 'form-control';

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.text = '-- bitte wählen --';
    select.appendChild(defaultOption);

    for (const key in options) {
        const opt = document.createElement('option');
        opt.value = key;
        opt.text = options[key];
        select.appendChild(opt);
    }
    return select;
}

function createInput() {
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'zusatz';
    input.className = 'form-control';
    input.placeholder = 'z. B. 1:2';
    return input;
}

function updateSpieler2Field(aktion, zusatzValue) {
    if (aktion === 'AUS') {
        spieler2Label.textContent = 'Eingewechselter Spieler';
        spieler2Icon.textContent = 'person_add';
        spieler2Wrapper.style.display = 'flex';
    } else if (aktion === '11mX' && zusatzValue === 'h') {
        spieler2Label.textContent = 'Torwart';
        spieler2Icon.textContent = 'sports_handball';
        spieler2Wrapper.style.display = 'flex';
    } else {
        spieler2Wrapper.style.display = 'none';
    }
}

function updateZusatzIcon(aktion) {
    const icon = zusatzWrapperGroup.querySelector('i.material-icons');
    if (!icon) return;

    switch (aktion) {
        case '11mX':
            icon.textContent = 'sports_handball';
            break;
        case 'AUS':
            icon.textContent = 'sync_alt';
            break;
        case 'TOR':
        case '11m':
        case 'ET':
            icon.textContent = 'sports_soccer';
            break;
        default:
            icon.textContent = 'info';
    }
}

aktionDropdown.addEventListener('change', function () {
    const value = this.value;
    zusatzWrapper.innerHTML = '';
    zusatzWrapperGroup.style.display = 'none';

    if (value in zusatzOptions) {
        zusatzWrapperGroup.style.display = 'flex';

        if (typeof zusatzOptions[value] === 'object') {
            const dropdown = createDropdown(zusatzOptions[value]);
            dropdown.addEventListener('change', function () {
                updateSpieler2Field(value, this.value);
            });
            zusatzWrapper.appendChild(dropdown);
        } else if (zusatzOptions[value] === 'resultat') {
            zusatzWrapper.appendChild(createInput());
        }
    }

    updateZusatzIcon(value);
    updateSpieler2Field(value, null);
});
</script>
