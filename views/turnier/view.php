<?php
use yii\helpers\Html;
use app\components\Helper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Turnier - $turniername $jahr";
?>
<div class="verein-page row">
    <!-- Widget 1: Vereinsdaten -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername $jahr - Teilnehmer") ?>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                <?php $anzahlSpieler =  0; ?>
                <?php $anzahlClubs =  0; ?>
                    <tbody>
                        <?php foreach ($clubs as $index => $club): ?>
                            <tr>
                                <td style="width: 50%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::img(Helper::getFlagUrl($club['land']), [
                                        'alt' => $club['name'],
                                        'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;',
                                    ]) ?>
                                    <?= Html::encode($club['name']) ?>
                                </td>
                                <td style="width: 10%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('Info', ['/club/view', 'id' => $club['id']], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 10%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                    <?= Html::a('Spiele', ['/spielplan/view', 'clubid' => $club['id'], 'jahr' => $jahr], ['class' => 'text-decoration-none']) ?>
                                </td>
                                <td style="width: 30%; background-color: <?= $index % 2 === 0 ? '#f0f8ff' : '#ffffff' ?> !important;">
                                <?php if ($club['spieleranzahl'] > 0): ?>
                                    <?= Html::a(
                                        "Kader",
                                        ['/kader/view', 'id' => $club['id'], 'year' => $jahr, 'turnier' => $wettbewerbID],
                                        ['class' => 'text-decoration-none']
                                    ) ?>
                                    <?= " ({$club['spieleranzahl']} Spieler)" ?>
                                    <?php $anzahlSpieler = $anzahlSpieler + $club['spieleranzahl'];?>
                                <?php else: ?>
                                    ----- 
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php $anzahlClubs = $anzahlClubs+1;?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

	<div class="col-md-1">&nbsp;</div>
    
    <!-- Widget 2: Zusammenfassung -->
	<div class="col-md-4">
		<div class="card">
			<div class="card-header"><h3>Übersicht</h3></div>
			<div class="card-body d-flex align-items-center">
				<table class="table">
                    <tr>
                        <th style="width: 20px;"><i class="fas fa-shield-alt"></i></th>
                        <td>Clubs: <?= $anzahlClubs; ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-users"></i></th>
                        <td>Spieler: <?= $anzahlSpieler; ?></td>
                    </tr> 
                    <tr>
                        <th style="width: 20px;"><i class="fas fa-futbol"></i></th>
                        <td>Tore: <?= $anzahlTore; ?></td>
                    </tr>
                    <tr>
                        <th>
							<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" fill="#1C75AC" stroke="#1C75AC" stroke-width="2">
								<path d="M22.213,3.663L11.14,.101c-1.325-.385-2.714,.377-3.099,1.696l-.661,2.203H2.5c-1.379,0-2.5,1.122-2.5,2.5V24H16v-1.22l2.311,.872L23.9,6.759c.385-1.324-.378-2.714-1.688-3.096Zm-7.213,19.337H1V6.5c0-.827,.673-1.5,1.5-1.5H13.5c.827,0,1.5,.673,1.5,1.5V23ZM22.945,6.462l-5.256,15.887-1.689-.638V6.5c0-1.378-1.121-2.5-2.5-2.5h-5.077l.576-1.919c.232-.793,1.067-1.25,1.848-1.024l11.073,3.562c.793,.231,1.251,1.066,1.025,1.843Z"/>
							</svg>	
    					</th>
                        <td>Platzverweise: <?= $anzahlPlatzverweise; ?></td>
                    </tr> 
				</table>
            </div>
        </div>
        
        <div>&nbsp;</div>

		<div class="card">
			<div class="card-header"><h3>Torjäger</h3></div>
			<div class="card-body d-flex align-items-center">
                <table class="table">
                    <tbody>
                        <?php foreach ($topScorers as $scorer): ?>
                        <tr>
                            <td>
                                 <?= Helper::getFlagUrl($scorer['nati1']) ? Html::img(Helper::getFlagUrl($scorer['nati1']), ['alt' => $scorer['nati1'] , 'style' => 'width: 25px; height: 20px; border-radius: 5px; border: 1px solid darkgrey; margin-right: 8px;']) : '' ?>
                            </td>
                            <td class="truncate-cell">
								<?php $spielername = htmlspecialchars($scorer['name']); ?>
                                <?php if (!empty($scorer['vorname'])): ?>
                                   <?php $spielername = $spielername . ', ' . htmlspecialchars(mb_substr($scorer['vorname'], 0, 1)) . '.'; ?>
                                <?php endif; ?>
                            	<?= Html::a($spielername, ['/spieler/view', 'id' => $scorer['id']], ['class' => 'text-decoration-none']) ?>
                            </td>
                            <td><?= (int)$scorer['tor']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                        	<td colspan="3" style="padding-top: 25px; border: 0; text-align: right;">
								<?= Html::a('Zur kompletten Liste', ['/torjaeger/view', 'wettbewerb' => $wettbewerbID, 'jahr' => $jahr], ['class' => 'text-decoration-none']) ?>
							</td>
                       	</tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
            