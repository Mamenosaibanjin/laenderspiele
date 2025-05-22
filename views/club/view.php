<?php
use app\components\ButtonHelper;
use app\components\ClubHelper;
use app\components\GameHelper;
use app\components\PositionHelper;
use app\components\Helper;
use app\models\Club;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\components\SquadHelper;

/* @var $this yii\web\View */
/* @var $club app\models\Club */
/* @var $nation app\models\Nation */
/* @var $stadium app\models\Stadion */
/* @var $recentMatches app\models\Spiel[] */
/* @var $upcomingMatches app\models\Spiel[] */
/* @var $squad app\models\Spieler[] */

$this->registerJsFile('@web/js/club.js',  ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = $isEditing
? ($club->isNewRecord
    ? Yii::t('app', 'Create New Club (#{id})', ['id' => Club::find()->max('id') + 1])
    : Yii::t('app', 'Edit Club: {name}', ['name' => $club->name])): $club->namevoll;
    $currentYear = substr(SquadHelper::getLastSquadYear($club->id),0,4);
    ?>

<?php 
$fields = [
    ['attribute' => 'name', 'icon' => 'fas fa-shield-alt', 'options' => ['maxlength' => true]],
    ['attribute' => 'namevoll', 'icon' => 'fas fa-address-card', 'options' => ['maxlength' => true]],
    ['attribute' => 'nations', 'icon' => 'fas fa-earth-europe', 'options' => []],
    ['attribute' => 'type', 'icon' => 'fas fa-layer-group', 'options' => [], 'data' => $vereine],
    ['attribute' => 'founded', 'icon' => 'fas fa-calendar-alt', 'options' => ['type' => 'date']],
    ['attribute' => 'colors', 'icon' => 'fas fa-palette', 'options' => []],
    ['attribute' => 'stadium', 'icon' => 'fas fa-location-dot', 'options' => [], 'data' => $stadien],
    ['attribute' => 'address', 'icon' => 'fas fa-envelope', 'options' => ['maxlength' => true]],
    ['attribute' => 'telefon', 'icon' => 'fas fa-phone', 'options' => ['maxlength' => true]],
    ['attribute' => 'homepage', 'icon' => 'fas fa-laptop-code', 'options' => ['maxlength' => true]],
    ['attribute' => 'email', 'icon' => 'fas fa-at', 'options' => ['maxlength' => true]],
];
?>

<div class="verein-page">

    <!-- Erste Widgetreihe -->
    <div class="row mb-3">
        <!-- Widget 1: Vereinsdaten -->
         <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                	<h3>
                    	<?php if (!$isEditing): ?>
                    		<?= $club->namevoll;?>
                    	<?php else : ?>
                             <?= $club->isNewRecord
                                ? Yii::t('app', 'Create New Club (#{id})', ['id' => Club::find()->max('id') + 1])
                                : Yii::t('app', 'Edit Club: {name}', ['name' => $club->namevoll]); ?>
                        <?php endif; ?>
					</h3>
				</div>
                <div class="card-body">
                    <?php if ($isEditing): ?>
                        <?php $form = ActiveForm::begin(); ?>
                            <table class="table">
                                <?php foreach ($fields as $field): ?>
                                    <?= ClubHelper::renderEditableRow($form, $club, $field['attribute'], $field['icon'], $field['options'], $field['data'] ?? null) ?>
                                <?php endforeach; ?>
                            </table>
                            <div class="form-group">
                                <?= ButtonHelper::saveButton() ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    <?php else: ?>
                        <table class="table">
                        <?php $i = 0;?>
                            <?php foreach ($fields as $field):?>
        						<?php 
        						    // Bestimme den zu übergebenen Wert: $club oder $stadion
                                    $value = ($field['attribute'] === 'stadium') ? $stadium : $club; 
                                ?>
                                <?= ClubHelper::renderViewRow($field['attribute'], $value, $field['icon']) ?>
                                <?php $i++;?>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

		<div class="col-md-2">&nbsp;</div>

        <!-- Widget 2: Zusammenfassung -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                	<h3>
                    	<?= Html::encode(ClubHelper::getLocalizedName($club)) ?>
                	</h3>
                </div>
                <div class="card-body d-flex align-items-center">
                    <!-- Bild als separater Div-Container -->
                    <div class="text-center" style="flex: 0 0 auto; margin-right: 20px;">
                        <img src="<?= Html::encode(Helper::getClubLogoUrl($club->id)) ?>" 
                             class="img-fluid" 
                             alt="<?= Html::encode($club->name) ?>" 
                             style="width: 100px; height: 100px;">
                    </div>
                    
                    <!-- Informationen im Row-Container -->
                    <div class="flex-grow-1">
                        <p class="text-center"><?= Html::encode($club->namevoll) ?></p>
                        <hr>
                        <div class="row">
                            <?= ClubHelper::renderCountryDivRow($club->land) ?>
                            <?= ClubHelper::renderFoundationDivRow($club->founded) ?>
                            <?= ClubHelper::renderStadiumDivRow($stadium) ?>
                            <?= ClubHelper::renderHomepageDivRow($club->homepage) ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php if ($recentMatches || $upcomingMatches): ?>
        <!-- Zweite Widgetreihe -->
        <div class="row mb-3">
 
            <!-- Widget: Letzte 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', Yii::t('app', 'Last {number} Games', ['number' => 5])) ?></div>
                    <div class="card-body">
                        <?= GameHelper::renderMatchWidget(Yii::t('app', Yii::t('app', 'Last {number} Games', ['number' => 5])), $recentMatches, $club, Yii::t('app', 'No Games found')) ?>
                    </div>
                </div>
            </div>
    
            <!-- Widget: Kommende 5 Spiele -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= Yii::t('app', Yii::t('app', 'Next {number} Games', ['number' => 5])) ?></div>
                    <div class="card-body">
                        <?= GameHelper::renderMatchWidget(Yii::t('app', Yii::t('app', 'Next {number} Games', ['number' => 5])), $upcomingMatches, $club, Yii::t('app', 'No Games planned')) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Widget 5: Squad -->
    <?php if ($squad || $nationalSquad): ?>
       <?php 
       // Mapping-Array für Positionen
        $positionMapping = PositionHelper::getMapping(['TW', 'AB', 'MF', 'ST', 'TR']);
        $title = '';
        $url = '';
        
        if ($squad) :
            $title = Yii::t('app', 'Season') . ' ' . ($currentYear - 1) . '/' . $currentYear;
            $url = ['/kader/' . $currentYear . '/' . $club->id];
        else :
            $squad = $nationalSquad;
            $lastMatch = Club::getLastMatch($club->id);
            $wettbewerbID = $lastMatch['wettbewerbID'] ?? null;
            $jahr = $lastMatch['jahr'] ?? null;
            $title = Helper::getTurniernameFullname($wettbewerbID, $jahr);
            $url = ['kader/' . $club->id . '/' . $jahr . '/' . $wettbewerbID];
        endif;
        ?>
        
        <div class="card"> <!-- Gesamtrahmen für den Kader -->
            <div class="card-header">
                <h3><?= Yii::t('app', 'Squad') ?></h3> <!-- Überschrift für den gesamten Abschnitt -->
            </div>
            <div class="card-body">
                <!-- Kader anzeigen, falls vorhanden -->
                <?php SquadHelper::renderSquad(
                    $squad, 
                    $positionMapping, 
                    $title,
                    $url,
                    Yii::t('app', 'Complete Squad')
                );
                ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php
$this->registerJs("$('.selectpicker').selectpicker();", \yii\web\View::POS_READY);
?>