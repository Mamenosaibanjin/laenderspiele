<?php
use yii\helpers\Html;
use app\components\Helper;
use app\components\SpielerHelper;
use app\components\TurnierHelper;
use app\models\Turnier;
use app\components\ClubHelper;

/** @var array $turnier */
/** @var app\models\Turnier[] $spiele */
/** @var string $turniername */
/** @var int $jahr */

$this->title = "Unfairste Spiele - $turniername";

?>
<div class="verein-page row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <?= Html::encode("$turniername - Unfairste Spiele") ?>
                </h3>
            </div>
            <div class="card-body">
            <div class="filter-box-spieler mb-3">
            Die Berechnung der Punkte (<b>P</b>) erfolgt nach folgenden Kriterien:<br><br>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:#CC0000; height: 15px; fill: currentColor;">
    			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    		</svg>
			 = 3 Punkte<br>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-sentry-element="Svg" data-sentry-component="TwoCards" data-sentry-source-file="icons.tsx" style="height: 15px;">
    			 <path fill="#F8D94D" d="M6.06 23.98c-.49.1-.89-.2-.99-.59C4.57 21.5.53 6.37.02 4.47c-.1-.49.2-.89.6-.99 0 0 18.912 16.02 18.91 15.95.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    			 <path fill="#C00" d="M.62 3.48C1.9 3.14 12.2.37 13.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82C19.53 19.587.62 3.48.62 3.48z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    		</svg>
			 = 2 Punkte<br>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:rgb(247,216,123); height: 15px; fill: currentColor;">
    	    	<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	    </svg>
    	     = 1 Punkt<br>
            </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                        	<th>Datum</th>
                            <th colspan="3" style="text-align: center;">Partie</th>
							<th>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:#CC0000; height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>
							</th>
							<th>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-sentry-element="Svg" data-sentry-component="TwoCards" data-sentry-source-file="icons.tsx" style="height: 15px;">
    	                			 <path fill="#F8D94D" d="M6.06 23.98c-.49.1-.89-.2-.99-.59C4.57 21.5.53 6.37.02 4.47c-.1-.49.2-.89.6-.99 0 0 18.912 16.02 18.91 15.95.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                			 <path fill="#C00" d="M.62 3.48C1.9 3.14 12.2.37 13.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82C19.53 19.587.62 3.48.62 3.48z" data-sentry-element="path" data-sentry-source-file="icons.tsx"></path>
    	                		</svg>
                            <th>
                            	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="color:rgb(247,216,123); height: 15px; fill: currentColor;">
    	                			<path d="M8.06 23.98c-.49.1-.89-.2-.99-.59-.5-1.89-4.54-17.02-5.05-18.92-.1-.49.2-.89.6-.99C3.9 3.14 14.2.37 15.49.02c.5-.1.89.2.99.59.51 1.88 4.55 16.93 5.05 18.82.2.49-.1.99-.59 1.09-2.58.69-11.59 3.11-12.88 3.46z"></path>
    	                		</svg>
    	                	</th>
    	                	<th><b>P</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unfairsteSpiele as $t): ?>
                        
                            <tr>
                                <td width="20%"><?= Helper::getFormattedDate($t['datum'])?></td>
                                <td align="right" width="25%">
                                	<?= Helper::getClubName($t['club1ID']) . " " .  Html::img(
                                	            Helper::getClubLogoUrl($t['club1ID']),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            )?>
                                </td>
                                <td width="10%" style="text-align: center;"><?= Html::a($t['tore1'] . ':' .  $t['tore2'], ['spielbericht/view', 'id' => $t['id']], ['class' => 'text-decoration-none']) ?></td>
                                <td width="25%">
                                	<?= Html::img(
                                	            Helper::getClubLogoUrl($t['club2ID']),
                                	            ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                                	            ) . Helper::getClubName($t['club2ID'])?>
                                </td>
                                <td width="5%"><?= $t['rk']?></td>
                                <td width="5%"><?= $t['grk']?></td>
                                <td width="5%"><?= $t['gk']?></td>
                                <td width="5%"><?= $t['punkte']?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>
            