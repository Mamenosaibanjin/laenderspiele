<?php
namespace app\components;

use app\components\Helper;
use app\models\Tournament;
use app\models\Wettbewerb;
use Yii;
use yii\db\Query;
use yii\helpers\Html;
use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNumeric;

class SpielerHelper
{
    public static function renderEditableRow($form, $spieler, $field, $labelIcon, $options = [])
    {
        $inputs = '';
        switch ($field) {
            case 'geburtstag':
                
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($spieler, 'geburtstag')->input('date', [
                'value' => $spieler->geburtstag ?: '',
                ])->label(false) .
                $form->field($spieler, 'geburtsort')->textInput([])->label(false) .
                
                $form->field($spieler, 'geburtsland')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
            case 'nati1':
                $inputs = Html::beginTag('div', ['class' => 'dropdown-container']) .
                $form->field($spieler, 'nati1')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($spieler, 'nati2')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                $form->field($spieler, 'nati3')->dropDownList(
                Helper::getCurrentNationsOptions(),
                [
                'prompt' => Yii::t('app', 'Choose a country'),
                'class' => 'form-control'
                    ]
                )->label(false) .
                Html::endTag('div');
                break;
                
                
            case 'spielfuss':
                
                $inputs = $form->field($spieler, 'spielfuss')->dropDownList(
                Helper::getSpielfussOptions(),
                [
                'prompt' => Yii::t('app', 'Choose Foot'),
                'class' => 'form-control'
                    ]
                )->label(false);
                break;
                
                
            default:
                $inputs = $form->field($spieler, $field)->textInput($options)->label(false);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $inputs)
            );
    }

    public static function renderViewRow($field, $spieler, $labelIcon = null)
    {
        
        if ($spieler->$field == '') : return ''; endif;
        
        $value = $spieler->$field ?? 'Unbekannt';
        switch ($field) {
            case 'geburtstag':
                $birthdate = $spieler->geburtstag ?: null; // Falls kein Geburtsdatum vorhanden, bleibt es null
                $countryFlag = Helper::getFlagInfo($spieler->geburtsland, $birthdate ?? date('Y-m-d'));
                
                if ($birthdate) {
                    $value = Yii::$app->formatter->asDate($birthdate, 'long') . ", " . Html::encode($spieler->geburtsort) . " " . $countryFlag;
                } else {
                    $value = Html::encode($spieler->geburtsort) . " " . $countryFlag;
                }
                break;
                
            case 'nati1':
                $value = '';
                foreach (['nati1', 'nati2', 'nati3'] as $field) {
                    if (!empty($spieler->$field)) {
                        $value .= Helper::getFlagInfo($spieler->$field, date('Y-m-d')) . "<br>";
                    }
                }
                $value = trim($value) ?: 'Unbekannt';
                break;
                
            case 'homepage':
                $url = Html::encode($spieler->homepage);
                $value = Html::a($url, "http://$url", ['target' => '_blank']);
                break;
                
            case 'facebook':
                $username = Html::encode($spieler->facebook);
                $value = Html::a("$username", "http://www.facebook.com/$username", ['target' => '_blank']);
                break;
                
            case 'instagram':
                $username = Html::encode($spieler->instagram);
                $value = Html::a("$username", "http://www.instagram.com/$username", ['target' => '_blank']);
                break;
                
            default:
                $value = Html::encode($value);
                break;
        }
        
        return Html::tag(
            'tr',
            Html::tag('th', Html::tag('i', '', ['class' => $labelIcon])) .
            Html::tag('td', $value)
            );
    }
    
    public static function renderEditableRowMulti($form, $spieler, $fields, $labelIcon, $options = [])
    {
        $index = $options['index'] ?? 0; // Index aus Optionen holen
        $cells = '';
        
        foreach ($fields as $field) {
            $inputs = '';
            switch ($field) {
                case 'von':
                    $vonValue = $spieler->von ? substr($spieler->von, 0, 4) . '-' . substr($spieler->von, 4, 2) : ''; // Default-Wert bei NULL
                    $inputs = $form->field($spieler, "[$index]von")->input('month', [
                        'value' => $vonValue,
                        'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'bis':
                    $bisValue = $spieler->bis ? substr($spieler->bis, 0, 4) . '-' . substr($spieler->bis, 4, 2) : ''; // Default-Wert bei NULL
                    $inputs = $form->field($spieler, "[$index]bis")->input('month', [
                        'value' => $bisValue,
                        'style' => 'width: 140px;',
                    ])->label(false);
                    break;
                    
                case 'verein':
                    $vereine = $options['vereine'] ?? [];
                    $vereinId = is_object($spieler->verein) ? $spieler->verein->id : ($spieler->verein ?? 0); // Fallback auf 0 bei NULL
                    $vereinName = $vereinId ? Helper::getClubName($vereinId) . ' (' . Helper::getClubNation($vereinId) . ')' : '';
                    
                    $vereinsDaten = json_encode(array_map(function ($verein) {
                        return [
                            'label' => Html::encode($verein['name']),
                            'value' => $verein['id'],
                            'klarname' => Html::encode($verein['name'] . " (" . $verein['land'] . ")"),
                        ];
                    }, $vereine));
                        
                        $inputs =
                        Html::hiddenInput("SpielerVereinSaison[$index][verein]", $vereinId, [
                            'id' => "hidden-verein-id-$index",
                        ]) .
                        Html::beginTag('div', ['class' => 'input-group']) .
                        Html::textInput("[$index]vereinName", $vereinName, [
                            'id' => "autocomplete-verein-$index",
                            'class' => 'form-control',
                            'data-vereine' => $vereinsDaten,
                            'placeholder' => Yii::t('app', 'Search for a club'),
                        ]) .
                        ($vereinId
                            ? Html::tag('span',
                                Html::a('<i class="fas fa-edit"></i>', ['club/view', 'id' => $vereinId], [
                                    'target' => '_blank',
                                    'class' => 'btn btn-outline-secondary',
                                    'title' => 'Verein bearbeiten',
                                    'aria-label' => 'Verein bearbeiten'
                                ]),
                                ['class' => 'input-group-append']
                                )
                            : ''
                            ) .
                            Html::endTag('div');
                            break;
                            
                        
                case 'nation':
                    $nationen = $options['nationen'] ?? [];
                    $landId = $spieler->landID ?? null;
                    $landName = $landId ? Helper::getClubName($landId) . ' (' . Helper::getClubNation($landId) . ')' : '';
                    
                    $nationenDaten = json_encode(array_map(function ($land) {
                        
                        //$land = Helper::getClubNation($verein['id']);  // Dynamisch das Land aus der Helper-Methode holen
                        return [
                            'label' => Html::encode($land['name']),
                            'value' => $land['id'],
                            'klarname' => Html::encode($land['name'] . " (" . $land['land'] . ")"),
                        ];
                    }, $nationen));
                   
                        $inputs =
                        Html::hiddenInput("SpielerLandWettbewerb[$index][land]", $landId, [
                            'id' => "hidden-land-id-$index",
                        ]) .
                        Html::textInput("[$index]landName", $landName, [
                            'id' => "autocomplete-land-$index",
                            'class' => 'form-control',
                            'data-nationen' => $nationenDaten,
                            'placeholder' => Yii::t('app', 'Search for a nation'),
                        ]);
                        break;

                case 'wettbewerb':
                    $tournaments = $options['tournaments'] ?? [];
                    $tournamentId = $spieler->tournamentID ?? null; // tournamentID oder NULL
                    $wettbewerbName = $tournamentId ? Helper::getTournamentName($tournamentId) : '';
                    $tournamentDaten = json_encode(array_map(function ($tournament) {
                        return [
                            'label' => Html::encode(
                                $tournament['wettbewerb_name'] .
                                " (" . $tournament['jahr'] .
                                ($tournament['land'] !== null ? " - " . $tournament['land'] : "") . ")"
                                ),
                            'value' => $tournament['id'],
                            'klarname' => Html::encode(
                                $tournament['wettbewerb_name'] .
                                " (" . $tournament['jahr'] .
                                ($tournament['land'] !== null ? " - " . $tournament['land'] : "") . ")"
                                ),
                        ];
                    }, $tournaments));
                        
                        // Info-Fenster für alte Einträge ohne tournamentID
                        $infoText = '';
                        if (!$tournamentId && $spieler->wettbewerbID) {
                            $wettbewerbInfo = Helper::getWettbewerbInfo($spieler->wettbewerbID);
                            if ($wettbewerbInfo) {
                                $infoText = "Fehlende Zuordnung! Wettbewerb: {$wettbewerbInfo['name']} ({$wettbewerbInfo['jahr']} - {$wettbewerbInfo['land']})";
                            }
                        }

                        $inputs =
                        Html::hiddenInput("SpielerLandWettbewerb[$index][tournamentID]", $tournamentId, [
                            'id' => "hidden-tournament-id-$index",
                        ]) .
                        Html::textInput("[$index]wettbewerbName", $wettbewerbName, [
                            'id' => "autocomplete-wettbewerb-$index",
                            'class' => 'form-control',
                            'data-tournaments' => $tournamentDaten,
                            'placeholder' => Yii::t('app', 'Search for a tournament'),
                        ]);
                        
                        if ($infoText) {
                            $inputs .= Html::tag('div', $infoText, ['class' => 'alert alert-warning mt-2']);
                        }
                        
                        break;
                        
                        
                case 'land':
                    $inputs = $form->field($spieler, "[$index]land")->dropDownList(
                    Helper::getNationenOptions(),
                    [
                    'prompt' => Yii::t('app', 'Choose a country'),
                    'class' => 'form-control',
                    ]
                    )->label(false);
                    break;
                    
                case 'position':
                    $positionen = $options['positionen'] ?? [];
                    $positionId = is_object($spieler->position) ? $spieler->position->id : ($spieler->position ?? ''); // Fallback auf leeren String bei NULL
                    
                    $inputs = $form->field($spieler, "[$index]position")->dropDownList(
                        Helper::getAllPositions(),
                        [
                            'prompt' => Yii::t('app', 'Choose a position'),
                            'class' => 'form-control',
                            'options' => [$positionId => ['Selected' => true]], // Vorbelegung setzen
                        ]
                        )->label(false);
                        break;
                        
                case 'buttons':
                    $inputs =
                    Html::submitButton(Yii::t('app', 'Save'), [
                    'class' => 'btn btn-primary btn-sm',
                    ]) .
                    " " .
                    Html::checkbox("SpielerLandWettbewerb[$index][delete]", false, [
                    'id' => "delete-switch-$index",
                    'value' => '1',
                    'class' => 'd-none',
                    ]) .
                    Html::button(Yii::t('app', 'X'), [
                    'class' => 'btn btn-danger btn-sm',
                    'onclick' => "$('#delete-switch-$index').prop('checked', true); $(this).closest('form').submit();",
                    'data-confirm' => Yii::t('app', 'Möchten Sie diesen Eintrag wirklich löschen?'),
                    ]);
                    break;
                        
                case 'buttonsMitJugend':
                    $inputs =
                    Html::tag('div',
                    Html::checkbox("SpielerVereinSaison[$index][jugend]", in_array($spieler->jugend, [1, '1'], true), [
                    'id' => "jugend-switch-$index",
                    'autocomplete' => 'off',
                    'value' => '1',
                    ]) . 
                    Html::label(Yii::t('app', 'Youth'), "jugend-switch-$index", [
                    'class' => 'btn btn-secondary btn-sm',
                    'style' => 'margin-left: 7px;',
                    ]),
                    ['class' => 'btn-group-toggle', 'data-toggle' => 'buttons', 'style' => 'float: left; padding-right: 7px;']
                    ) . " " .
                        Html::submitButton(Yii::t('app', 'Save'), [
                        'class' => 'btn btn-primary btn-sm',
                        ]) .
                        " " .
                        Html::checkbox("SpielerVereinSaison[$index][delete]", false, [
                        'id' => "delete-switch-nation-$index",
                        'value' => '1',
                        'class' => 'd-none',
                        ]) .
                        Html::button(Yii::t('app', 'X'), [
                        'class' => 'btn btn-danger btn-sm',
                        'onclick' => "$('#delete-switch-nation-$index').prop('checked', true); $(this).closest('form').submit();",
                        'data-confirm' => Yii::t('app', 'Möchten Sie diesen Eintrag wirklich löschen?'),
                        ]);
                        break;
                        
                default:
                    $inputs = $form->field($spieler, "[$index]$field")->textInput($options)->label(false);
                    break;
            }
            
            $cells .= Html::tag('td', $inputs);
        }
        
        return Html::tag('tr', $cells);
    }
    
    public static function renderViewRowMulti($karriereDaten, $fields, $options = [])
    {
        $rows = ''; // Endgültiger HTML-String
        $index = $options['index'] ?? 0; // Fallback für Index
        
        $lastKnownBis = null; // Letztes bekanntes 'bis'-Datum
        
        foreach ($karriereDaten as $daten) {
            $rowsArray = []; // Speichert Zeiträume für diese Karriere-Zeile
            $verein = $daten['verein'] ?? null;
            $vereinId = is_object($verein) ? $verein->id : $verein;
            $nation = $vereinId ? Helper::getClubNation($vereinId) : null;
            
            $vonDatum = !empty($daten['von']) ? Helper::convertToDate($daten['von']) : null;
            $bisDatum = !empty($daten['bis']) ? Helper::convertToDate($daten['bis']) : null;
            
            // Fallback-Logik für NULL-Werte
            $vonDatumShown = $vonDatum;
            $bisDatumShown = $bisDatum;
            $vonDatum = self::getFallbackDate($vonDatum, $bisDatum, $lastKnownBis);
            $bisDatum = self::getFallbackDate($bisDatum, $vonDatum, $lastKnownBis);
                        
            if (!$vonDatum || !$bisDatum) {
                continue; // Falls beide NULL sind, überspringen
            }
            
            // Letztes bekanntes 'bis' aktualisieren
            $lastKnownBis = $bisDatum;
            
            // 🔍 Flaggenwechsel abrufen
            $flaggenWechsel = (new Query())
            ->select(['startdatum', 'key'])
            ->from('flags')
            ->where(['key' => $nation])
            ->andWhere([
                'AND',
                ['key' => $nation],
                ['OR',
                    ['>=', 'startdatum', $vonDatum],
                    ['>=', 'enddatum', $vonDatum],
                    ['enddatum' => null]
                ],
                ['OR',
                    ['<=', 'startdatum', $bisDatum],
                    ['<=', 'enddatum', $bisDatum],
                    ['enddatum' => null]
                ]
            ])
            ->orderBy(['startdatum' => SORT_ASC]) // 🔄 Aufsteigend nach Startdatum
            ->all();
            
            // 🏁 Startwert für die Iteration
            $zeitraumStart = $vonDatum;
            $wechselZeiten = [];
            
            foreach ($flaggenWechsel as $wechsel) {
                $wechselDatum = $wechsel['startdatum'];
                
                if ($wechselDatum <= $zeitraumStart || $wechselDatum > $bisDatum) {
                    continue; // Nur relevante Wechsel berücksichtigen
                }
                
                $wechselZeiten[] = $wechselDatum;
            }
            
            // Falls es Wechsel gibt, Zeiträume aufteilen
            if (!empty($wechselZeiten)) {
                $wechselZeiten[] = $bisDatum; // Letzten Zeitraum beenden
                
                foreach ($wechselZeiten as $wechselDatum) {
                    $flagInfo = Helper::getFlagInfo($nation, $zeitraumStart, false, $wechselDatum);
                    $rowsArray[] = self::generateTableCells($fields, $zeitraumStart, $wechselDatum, $vereinId, $daten, $flagInfo);
                    $zeitraumStart = $wechselDatum; // Neuer Startpunkt
                }
            } else {
                // Falls keine Flaggenwechsel, einfach den Originalzeitraum übernehmen
                $flagInfo = Helper::getFlagInfo($nation, $vonDatum, false, $bisDatum);
                $rowsArray[] = self::generateTableCells($fields, $vonDatumShown, $bisDatumShown, $vereinId, $daten, $flagInfo);
            }
            
            // 🔄 Nur innerhalb dieser Karriere-Zeile umkehren
            $rowsArray = array_reverse($rowsArray);
            
            // 📝 Diese Zeilen in die Hauptausgabe einfügen
            $rows .= implode('', array_map(fn($cells) => Html::tag('tr', $cells), $rowsArray));
        }
        
        return $rows;
    }
    
    
    private static function getFallbackDate($von, $bis, &$lastKnownBis)
    {
        if ($von === null && $bis !== null) {
            $lastKnownBis = $bis;
            return $bis; // Wenn 'von' fehlt, nutzen wir 'bis'
        }
        
        if ($von !== null && $bis === null) {
            return $lastKnownBis ?? $von; // Falls 'bis' fehlt, nutzen wir das letzte bekannte 'bis' oder 'von'
        }
        
        if ($von === null && $bis === null) {
            return $lastKnownBis; // Falls beide fehlen, nehmen wir das letzte bekannte 'bis'
        }
        
        $lastKnownBis = $bis; // 'bis' speichern, um es für spätere fehlende Werte zu nutzen
        return $von; // Standardfall: 'von' ist gesetzt
    }
    
    /**
     * Hilfsfunktion zur Generierung von Tabellenzellen
     */
    private static function generateTableCells($fields, $start, $end, $vereinId, $daten, $flagInfo)
    {
        $cells = '';
        foreach ($fields as $field) {
            switch ($field) {
                case 'zeitraum':
                    $formattedStart = $start ? Helper::formatDate($start) : '???';
                    $formattedEnd = $end ? Helper::formatDate($end) : '???';
                    $value = "{$formattedStart} - {$formattedEnd}";
                    break;
                case 'verein':
                    $value = $vereinId
                    ? Html::img(Helper::getClubLogoUrl($vereinId), ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']) .
                    Html::a(Html::encode(Helper::getClubName($vereinId)), ['/club/view', 'id' => $vereinId], ['class' => 'text-decoration-none'])
                    : Yii::t('app', 'Unknown Club');
                    break;
                case 'land':
                    $value = $flagInfo;
                    break;
                case 'position':
                    $position = $daten['position'] ?? null;
                    $positionId = is_object($position) ? $position->id : $position;
                    $value = isNumeric($positionId) ? Helper::getPosition($positionId) : Yii::t('app', 'Unknown Position');
                    break;
                default:
                    $value = $daten[$field] ?? Yii::t('app', 'Unknown');
                    break;
            }
            $cells .= Html::tag('td', $value);
        }
        return $cells;
    }
    
    
    public static function renderViewRowMultiNation($karriereDaten, $fields, $options = [])
    {
        $rows = '';
        $index = $options['index'] ?? 0; // Fallback für Index
        
        foreach ($karriereDaten as $daten) {
            $cells = '';
            foreach ($fields as $field) {
                $value = '';
                switch ($field) {
                    case 'zeitraum':
                        $von = isset($daten['von']) ? substr($daten['von'], 0, 4) . '/' . substr($daten['von'], 4, 2) : '';
                        $bis = isset($daten['bis']) ? substr($daten['bis'], 0, 4) . '/' . substr($daten['bis'], 4, 2) : '';
                        $value = "$von - $bis";
                        break;
                        
                    case 'verein':
                        $verein = $daten['verein'] ?? null;
                        $vereinId = is_object($verein) ? $verein->id : $verein; // ID aus dem Objekt extrahieren
                        $value = $vereinId
                        ? Html::img(Helper::getClubLogoUrl($vereinId), ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']) .
                        Html::a(Html::encode(Helper::getClubName($vereinId)), ['/club/view', 'id' => $vereinId], ['class' => 'text-decoration-none'])
                        : Yii::t('app', 'Unknown Club');
                        break;
                        
                    case 'land':
                        $verein = $daten['verein'] ?? null;
                        $vereinId = is_object($verein) ? $verein->id : $verein; // ID aus dem Objekt extrahieren
                        $nation = $vereinId ? Helper::getClubNation($vereinId) : null;
                        
                        if (!empty($daten['von'])) {
                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $daten['von'])) {
                                // Bereits im Format YYYY-MM-DD
                                $startdatum = $daten['von'];
                            } else {
                                // Format YYYYMM → Umwandeln in YYYY-MM-DD
                                $startdatum = substr($daten['von'], 0, 4) . '-' . substr($daten['von'], 4, 2) . '-01';
                            }
                        } else {
                            $startdatum = null;
                        }
                        
                        $value = $nation
                        ? Helper::getFlagInfo($nation, $startdatum, false)
                        : Yii::t('app', 'Unknown Country');
                        break;
                        
                    case 'position':
                        $position = $daten['position'] ?? null;
                        $positionId = is_object($position) ? $position->id : $position; // ID aus dem Objekt extrahieren
                        $value = isNumeric($positionId) ? Helper::getPosition($positionId) : Yii::t('app', 'Unknown Position');
                        break;
                        
                    case 'nation':
                        $verein = $daten['landID'] ?? null;
                        $vereinId = is_object($verein) ? $verein->id : $verein;
                        
                        $startdatum = $tournament->startdatum ? substr($tournament->startdatum, 0, 4) . '-' . substr($tournament->startdatum, 4, 2) . '-01' : null;
                        $nation = $vereinId ? Helper::getClubNation($vereinId) : null;
                        $value = $nation
                        ? Helper::getFlagInfo($nation, $startdatum, false) . ' ' . Html::encode(Helper::getClubName($vereinId))
                        : Yii::t('app', 'Unknown Country');
                        break;
                        
                    case 'tournament':
                        $tournamentId = $daten['tournamentID'] ?? null;
                        if ($tournamentId) {
                            $tournament = Tournament::findOne($tournamentId);
                            if ($tournament) {
                                $wettbewerb = Wettbewerb::findOne($tournament->wettbewerbID);
                                $wettbewerbName = $wettbewerb ? $wettbewerb->name : Yii::t('app', 'Unknown Tournament');
                                $jahr = $tournament->jahr ?? Yii::t('app', 'Unknown Year');
                                
                                // Länderdarstellung mit Mehrfach-Ländern
                                $laenderKeys = !empty($tournament->land) ? explode('/', $tournament->land) : [];
                                $landNamen = [];
                                foreach ($laenderKeys as $key) {
                                    $startdatum = $tournament->startdatum ? substr($tournament->startdatum, 0, 4) . '-' . substr($tournament->startdatum, 4, 2) . '-01' : null;
                                    $flagInfo = Helper::getFlagInfo($key, $startdatum, false);
                                    $landNamen[] = $flagInfo;
                                }
                                $landAnzeige = implode("", $landNamen);
                                
                                $value = "$landAnzeige $wettbewerbName $jahr";
                            }
                        } else {
                            $value = Yii::t('app', 'Unknown Tournament');
                        }
                        break;
                        
                    default:
                        $value = $daten[$field] ?? Yii::t('app', 'Unknown');
                        break;
                }
                
                $cells .= Html::tag('td', $value);
            }
            
            $rows .= Html::tag('tr', $cells);
            $index++;
        }
        
        return $rows;
    }
    
    public static function getNationId($spielerID, $tournamentID = null)
    {
        $clubID = (new \yii\db\Query())
        ->select(['landID'])
        ->from('spieler_land_wettbewerb')
        ->where(['spielerID' => $spielerID])
        ->andWhere(['tournamentID' => $tournamentID])
        ->all();
        
        return $clubID[0]['landID'];
        
    }
    
    public static function getBirthday($spielerId)
    {
        $birthday = (new \yii\db\Query())
        ->select(['geburtstag'])
        ->from('spieler')
        ->where(['id' => $spielerId])   // Optional: Alphabetische Sortierung
        ->all();
        
        return Helper::getFormattedDate($birthday[0]['geburtstag']);
        
    }
    
    public static function getHeight($spielerId)
    {
        $height = (new \yii\db\Query())
        ->select(['height'])
        ->from('spieler')
        ->where(['id' => $spielerId])   // Optional: Alphabetische Sortierung
        ->all();
        
        if ($height[0]['height']) :
            return $height[0]['height'] . " cm";
        else :
            return '';
        endif;
        
    }
    
    public static function getPosition($spielerID, $tournamentID = null)
    {
        $position = (new \yii\db\Query())
        ->select(['positionID'])
        ->from('spieler_land_wettbewerb')
        ->where(['spielerID' => $spielerID])
        ->andWhere(['tournamentID' => $tournamentID])
        ->all();
        
        return Helper::getPosition($position[0]['positionID']);
        
    }
    
    public static function getLandAtTournament($spielerID, $tournamentID)
    {
        $land = (new \yii\db\Query())
        ->select(['landID'])
        ->from('spieler_land_wettbewerb')
        ->where(['spielerID' => $spielerID])
        ->andWhere(['tournamentID' => $tournamentID])
        ->all();
                
        return Html::img(
                    Helper::getClubLogoUrl($land[0]['landID']), 
                    ['alt' => 'Logo', 'style' => 'height: 20px; margin-right: 5px;']
                    ) . " " . 
                Html::a(
                    Helper::getClubName($land[0]['landID']),
                    ['/club/view', 'id' => $land[0]['landID']],
                    ['class' => 'text-decoration-none']
                    );
    }
    
}
?>