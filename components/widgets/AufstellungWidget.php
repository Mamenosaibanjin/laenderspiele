<?php

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class AufstellungWidget extends Widget
{
    public $spiel;
    public $heim = true; // true = Heim, false = Auswärts
    public $wechsel;
    
    public function run()
    {
        return $this->render('@app/views/spielbericht/_widget_aufstellung', [
            'spiel' => $this->spiel,
            'heim' => $this->heim,
            'wechsel' => $this->wechsel,
        ]);
    }
}
