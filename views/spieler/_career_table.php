<?php use app\components\Helper;
use yii\helpers\Html;

foreach ($vereinsKarriere as $karriere): ?>
	<?php $dataid = $karriere->id;?>
    	<tr data-id='<?= $dataid ?>'>
            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
           	<span class="display-mode">
           		<?= Html::encode(Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->von)->format('Y-m-d'), 'MM/yyyy')) ?> - <?= Html::encode($karriere->bis ? Yii::$app->formatter->asDate(DateTime::createFromFormat('Ym', $karriere->bis)->format('Y-m-d'), 'MM/yyyy') : 'heute') ?>
           	</span>
        	<?php if ($isEditing): ?>
        		<input type="month" class="form-control edit-mode w-auto" name="von" id="von" value="<?= substr($karriere->von, 0, 4) . '-' . substr($karriere->von, 4, 2) ?>" style="width: 140px !important;">
    			<input type="month" class="form-control edit-mode w-auto" name="bis" id="bis" value="<?= substr($karriere->bis, 0, 4) . '-' . substr($karriere->bis, 4, 2) ?>" style="width: 140px !important;">
           	<?php endif; ?>
        </td>
        
        <?php if ($karriere->verein): ?>
            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>width: 35px; text-align: right;">
                <span class="display-mode">
                    <?= Html::img(Helper::getClubLogoUrl($karriere->verein->id), ['alt' => $karriere->verein->name, 'style' => 'height: 30px;']) ?>
                </span>
            </td>
            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>text-align: left;">
                <span class="display-mode">
                    <?= Html::a(Html::encode($karriere->verein->name), ['/club/view', 'id' => $karriere->verein->id], ['class' => 'text-decoration-none']) ?>
                </span>
                <?php if ($isEditing): ?>
                    <input type="text" class="form-control edit-mode verein-input" id="verein-input" list="vereine-list" value="<?= Html::encode($karriere->verein->name ?? '') ?>" autocomplete="off" style="width: 175px;">
                    <input type="hidden" name="vereinID" id="vereinID" value="<?= Html::encode($karriere->vereinID) ?>">
                    
                    <datalist id="vereine-list">
                        <?php foreach ($vereine as $verein): ?>
                            <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                <?php endif; ?>
            </td>
            <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'background-color: #79C01D !important; font-weight: bold;' : '' ?>">
                <span class="display-mode">
                    <?= Html::img(Helper::getFlagUrl($karriere->verein->land), ['alt' => $karriere->verein->land, 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) ?>
                </span>
            </td>
        <?php else: ?>
            <td colspan="3">
                <span class="display-mode"></span>
                <?php if ($isEditing): ?>
                    <div class="edit-mode" style="display: block;">
                        <input type="text" class="form-control" id="verein-input" list="vereine-list" value="" autocomplete="off" style="width: 175px;">
                        <input type="hidden" name="vereinID" id="vereinID" value="">
                        
                        <datalist id="vereine-list">
                            <?php foreach ($vereine as $verein): ?>
                                <option value="<?= Html::encode($verein->name) ?> (<?= Html::encode($verein->land) ?>)" data-id="<?= $verein->id ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                <?php endif; ?>
            </td>
        <?php endif; ?>

        <td style="<?= $karriere->von <= $currentMonth && ($karriere->bis >= $currentMonth || $karriere->bis === null) ? 'color: #1C75AC; background-color: #79C01D !important; font-weight: bold;' : '' ?>">
        	<span class="display-mode">
        		<?= Html::encode($karriere->position->positionKurz) ?>
        	</span>
        	<?php if ($isEditing): ?>
            	<select class="form-control edit-mode" name="positionID" id="positionID">
                    <?php foreach ($positionen as $position): ?>
                        <option value="<?= $position->id ?>" <?= $karriere->positionID == $position->id ? 'selected' : '' ?>><?= Html::encode($position->positionLang_de) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </td>
        <?php if ($isEditing): ?>
            <!-- Bootstrap Switch -->
			<td>
            	<div class="btn-group-toggle edit-mode" data-toggle="buttons">
                    <label class="btn btn-outline-primary btn-sm">
                        <input type="checkbox" name="jugend" id="jugend-switch" autocomplete="off"> Jugend
                    </label>
                </div>
				<!-- Buttons -->
                <button class="btn btn-primary btn-sm edit-button display-mode">Bearbeiten</button>
                <button class="btn btn-primary btn-sm save-button edit-mode" id="btn-save-clubs">Speichern</button>
                <button class="btn btn-secondary btn-sm cancel-button edit-mode">Abbrechen</button>
            </td>
        <?php endif; ?>                                    
	</tr>
<?php endforeach; ?>