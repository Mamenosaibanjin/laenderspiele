<?php
namespace app\models;

use Yii;

class Spielbericht extends \yii\base\Model
{
    public $spiel;
    public $aufstellung1;
    public $aufstellung2;
    public $games;
    public $referees;
    
    public function __construct($spiel)
    {
        $this->spiel = $spiel;
        $this->aufstellung1 = $spiel->aufstellung1;
        $this->aufstellung2 = $spiel->aufstellung2;
        $this->games = $spiel->games;
        $this->referees = [
            $spiel->referee1,
            $spiel->referee2,
            $spiel->referee3,
            $spiel->referee4,
        ];
    }
    
    public function getHighlights()
    {
        return array_filter($this->games, function ($game) {
            return in_array($game->aktion, ['TOR', '11m', 'ET', '11mX', 'RK', 'GRK']);
        });
    }
    
    public function getTore()
    {
        return array_filter($this->games, function ($game) {
            return in_array($game->aktion, ['TOR', '11m', 'ET']);
        });
    }
    
    public function getKarten()
    {
        return array_filter($this->games, function ($game) {
            return in_array($game->aktion, ['GK', 'RK', 'GRK']);
        });
    }
    
    public function getWechsel()
    {
        return array_filter($this->games, function ($game) {
            return in_array($game->aktion, ['AUS']);
        });
    }
    
    public function getBesondereEreignisse()
    {
        return array_filter($this->games, function ($game) {
            return in_array($game->aktion, ['11,X']);
        });
    }
    
    public function isHeimAktion($game)
    {
        return $this->spiel->isHeimAktion($game);
    }
    
    public function isAuswaertsAktion($game)
    {
        return $this->spiel->isAuswaertsAktion($game);
    }
}
?>