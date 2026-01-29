<?php

use yii\db\Migration;

class m260129_171718_create_tiebreak_drawing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tiebreak_drawing', [
            'tournament_id' => $this->integer()->notNull(),
            'runde_id'      => $this->integer()->notNull(),
            'points'        => $this->integer()->notNull(),
            'club_id'       => $this->integer()->notNull(),
            'draw_order'    => $this->integer()->notNull(),
        ]);
        
        $this->addPrimaryKey(
            'pk_tiebreak_drawing',
            'tiebreak_drawing',
            ['tournament_id', 'runde_id', 'points', 'club_id']
            );
        
        // Indizes (wichtig für Sortierung!)
        $this->createIndex(
            'idx_tbd_lookup',
            'tiebreak_drawing',
            ['tournament_id', 'runde_id', 'points']
            );
        
        // FKs (optional, aber empfohlen)
        $this->addForeignKey(
            'fk_tbd_tournament',
            'tiebreak_drawing',
            'tournament_id',
            'tournament',
            'id',
            'CASCADE'
            );
        
        $this->addForeignKey(
            'fk_tbd_club',
            'tiebreak_drawing',
            'club_id',
            'clubs',
            'id',
            'CASCADE'
            );
    }
    
    public function safeDown()
    {
        $this->dropTable('tiebreak_drawing');
    }
}
