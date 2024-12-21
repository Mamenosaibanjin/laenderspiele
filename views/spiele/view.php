<?php
use yii\helpers\Html;
use app\components\Helper;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Modal;
use yii\web\JqueryAsset;
$this->registerAssetBundle(JqueryAsset::class);
use yii\bootstrap5\BootstrapAsset;
$this->registerAssetBundle(BootstrapAsset::class);

/** @var array $spiele */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Spiele - $turniername $jahr";
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function initializeAwesomplete(inputId, hiddenInputId, url) {
        const input = document.getElementById(inputId);
        const hiddenInput = document.getElementById(hiddenInputId);

        if (input && hiddenInput) {
            const awesomplete = new Awesomplete(input, {
                minChars: 2,
                autoFirst: true,
            });

            input.addEventListener('input', function () {
                const term = input.value;
                fetch(url + '?term=' + term)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            // Setze die Liste auf den Namen, aber merke die ID
                            awesomplete.list = data.map(item => ({
                                label: item.value, // Der Text, der angezeigt wird
                                value: item.id,   // Die ID, die gespeichert wird
                            }));
                        } else {
                            console.error('Die Antwort ist kein Array:', data);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            input.addEventListener('awesomplete-selectcomplete', function (event) {
                // Speichere die ausgewählte ID im versteckten Feld
                hiddenInput.value = event.text.value; 
                input.value = event.text.label; // Zeige den Klartext an
            });
        }
    }

    // Initialisierung für club1ID
    initializeAwesomplete('club1Text', 'club1ID', '<?= \yii\helpers\Url::to(['club/search']) ?>');

    // Initialisierung für club2ID
    initializeAwesomplete('club2Text', 'club2ID', '<?= \yii\helpers\Url::to(['club/search']) ?>');

    // Initialisierung für wettbewerbID
    initializeAwesomplete('wettbewerbText', 'wettbewerbID', '<?= \yii\helpers\Url::to(['turnier/search']) ?>');
});

document.addEventListener('DOMContentLoaded', function () {
    // Öffnet das Eingabefeld bei Klick auf die Zeit
    document.querySelectorAll('.view-time').forEach(span => {
        span.addEventListener('click', function () {
            const spielId = this.dataset.spielId;

            // Verstecke den Text und zeige das Eingabefeld
            this.style.display = 'none';  // Versteckt das Textfeld
            const wrapper = document.querySelector(`.edit-wrapper[data-spiel-id="${spielId}"]`);
            if (wrapper) {
                wrapper.style.display = 'block';  // Zeigt das Eingabefeld
                const input = wrapper.querySelector('.edit-datetime');
                input.focus();
            }
        });
    });

    // Speichert Änderungen bei Verlust des Fokus
    document.querySelectorAll('.edit-datetime').forEach(input => {
        input.addEventListener('blur', function () {
        
            const spielId = this.dataset.spielId;
            const value = this.value;
            
            console.log('spiel ID:', spielId);
			console.log('Datetime:', value);
			
            // Sende die Änderung per AJAX
            fetch('<?= \yii\helpers\Url::to(['spiele/update-datetime']) ?>', {
                method: 'POST',
                 headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
                },
                body: JSON.stringify({ spielId, datetime: value })
            })
            .then(response => response.json())
            .then(data => {
            
                const wrapper = document.querySelector(`.edit-wrapper[data-spiel-id="${spielId}"]`);
                const span = document.querySelector(`.view-time[data-spiel-id="${spielId}"]`);

                if (data.success) {
                    // Aktualisiere die Zeit und zeige die Anzeige
                    if (span) {
                        const date = new Date(value);
                        span.textContent = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        span.style.display = 'block';  // Zeigt das Textfeld
                    }
                } else {
                    alert('Fehler: ' + data.error);
                }
                // Verstecke das Eingabefeld
                if (wrapper) wrapper.style.display = 'none';
            })
            .catch(error => console.error('Fehler:', error));
            
        });
    });

    // Schließe das Eingabefeld bei Escape
    document.querySelectorAll('.edit-datetime').forEach(input => {
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const wrapper = this.closest('.edit-wrapper');
                const span = document.querySelector(`.view-time[data-spiel-id="${this.dataset.spielId}"]`);

                if (wrapper) wrapper.style.display = 'none';  // Versteckt das Eingabefeld
                if (span) span.style.display = 'block';  // Zeigt das Textfeld
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Funktion für Inline-Editing
    document.querySelectorAll('.editable').forEach(function(cell) {
        cell.addEventListener('click', function() {
            const field = cell.getAttribute('data-field');
            const currentValue = cell.innerText.trim();

            // Erstelle das Eingabefeld
            const input = document.createElement('input');
            input.value = currentValue;

            // Füge es zur Zelle hinzu
            cell.innerHTML = '';
            cell.appendChild(input);

            // Focus auf das Eingabefeld und setze den Cursor ans Ende
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);

            input.addEventListener('blur', function() {
                // Bei Verlassen des Eingabefeldes die Änderung speichern
                const newValue = input.value;
                const id = cell.id.split('-')[1];

                // AJAX-Request zum Speichern der Änderungen
                fetch('/spiele/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        field: field,
                        value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Erfolgreich gespeichert, Update der Anzeige
                        cell.innerHTML = newValue;
                    } else {
                        // Fehlerbehandlung
                        alert('Fehler beim Speichern');
                    }
                });
            });
        });
    });
});
</script>


<div class="card">
    <div class="card-header">
        <h3>
            Spiele - <?= Html::encode("$turniername - $jahr") ?>
        </h3>
    </div>
    <div class="card-body">
        <table class="table">
             <tbody>
                <?php 
                $lastDate = null; // Variable für das letzte Datum
                foreach ($spiele as $spiel): 
                    $currentDate = $spiel->getFormattedDate(); // Aktuelles Datum
                ?>
                    <!-- Neue Zeile bei neuem Datum -->
                    <?php if ($lastDate !== $currentDate): ?>
                        <tr class="table-secondary">
                            <td colspan="4" class="text-left font-weight-bold">
                                <?= Html::encode($currentDate) ?>
                            </td>
                        </tr>
                        <?php $lastDate = $currentDate; // Aktualisiere das letzte Datum ?>
                    <?php endif; ?>
                    
                    <tr>
     <td style="position: relative;">
        <!-- Standardanzeige der Zeit -->
        <span class="view-time" data-spiel-id="<?= $spiel->spielID ?>" style="cursor: pointer;">
            <?= Html::encode($spiel->zeit ? Yii::$app->formatter->asTime($spiel->zeit, 'php:H:i') : '-') ?>
        </span>

        <!-- Wrapper für editierbares Feld -->
        <div class="edit-wrapper" data-spiel-id="<?= $spiel->spielID ?>" style="display: none; position: absolute; top: 0; left: 0; width: 100%; background: white; z-index: 10; padding: 2px; border: 1px solid lightgrey;">
            <?php 
            $timezone = new \DateTimeZone('Europe/London');  // Setze die gewünschte Zeitzone
            $datetime = new \DateTime($spiel->datum . ' ' . $spiel->zeit, new \DateTimeZone('UTC'));  // Eingabezeit in UTC
            $datetime->setTimezone($timezone);  // Umwandeln in die gewünschte Zeitzone
            $formattedTime = $datetime->format('Y-m-d\TH:i');  // Format für datetime-local
            ?>
            <input 
                type="datetime-local" 
                class="edit-datetime" 
                data-spiel-id="<?= $spiel->spielID ?>" 
                value="<?= Html::encode($formattedTime) ?>"
            />
        </div>
    </td>
                      <td style="text-align: right; width: 30%;"><?= Html::encode($spiel->club1->name ?? 'Unbekannt') ?> <?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->club1->id)), ['alt' => $spiel->club1->name , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?></td>
                        <td style="text-align: center; width: 10%;"><?= $spiel->getErgebnisHtml() ?></td>
                        <td style="width: 50%;"><?= Html::img(Helper::getFlagUrl(Helper::getClubNation($spiel->club2->id)), ['alt' => $spiel->club2->name , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?> <?= Html::encode($spiel->club2->name ?? 'Unbekannt') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

<?php 
 // Modal-Button
echo Html::button('Spiel hinzufügen', [
    'class' => 'btn btn-primary',
    'data-bs-toggle' => 'modal',
    'data-bs-target' => '#add-game-modal',
    'style' => 'background-color: #1C75AC !important;'
]);

Modal::begin([
    'id' => 'add-game-modal',
    'title' => 'Spiel hinzufügen',
    'size' => Modal::SIZE_LARGE,
]);

// Formular
$form = ActiveForm::begin([
    'id' => 'add-game-form',
    'action' => ['spiele/create'], // Die Route zum Speichern des Spiels
    'method' => 'post',
    'options' => ['data-pjax' => true],
]); ?>

<div class="form-group">
    <?= Html::textInput('club1Text', '', ['id' => 'club1Text', 'class' => 'form-control', 'placeholder' => 'Heimmannschaft suchen...']) ?>
    <?= Html::hiddenInput('club1ID', '', ['id' => 'club1ID']) ?>
</div>

<div class="form-group">
    <?= Html::textInput('club2Text', '', ['id' => 'club2Text', 'class' => 'form-control', 'placeholder' => 'Auswärtsmannschaft suchen...']) ?>
    <?= Html::hiddenInput('club2ID', '', ['id' => 'club2ID']) ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'datum')->input('date') ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'zeit')->input('time') ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'jahr')->textInput() ?>
</div>

<div class="form-group">
    <?= Html::textInput('wettbewerbText', '', ['id' => 'wettbewerbText', 'class' => 'form-control', 'placeholder' => 'Wettbewerb suchen...']) ?>
    <?= Html::hiddenInput('wettbewerbID', '', ['id' => 'wettbewerbID']) ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'gruppe')->dropDownList([
        '' => 'kein',
        '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5',
        'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E',
    ]) ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'runde')->dropDownList(array_combine(range(0, 15), range(0, 15))) ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'spieltag')->dropDownList(array_combine(range(0, 40), range(0, 40))) ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'beschriftung')->textarea() ?>
</div>


<div class="form-group">
    <?= Html::submitButton('Speichern', ['class' => 'btn btn-success', 'style' => 'background-color: #1C75AC !important;']) ?>
</div>

<?php ActiveForm::end();

Modal::end();
?>
    </div>
</div>
