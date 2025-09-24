<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tiebreak_rule}}`.
 */
class m250923_033334_create_tiebreak_rule_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tiebreak_rule}}', [
            'id' => $this->primaryKey(),
            'tournament_id' => $this->integer()->notNull(),
            'tiebreak_type_id' => $this->integer()->notNull(),
            'priority' => $this->integer()->notNull(),
        ]);
        
        // Foreign Keys
        $this->addForeignKey(
            'fk_tiebreak_rule_tournament',
            '{{%tiebreak_rule}}',
            'tournament_id',
            '{{%tournament}}',
            'id',
            'CASCADE'
            );
        
        $this->addForeignKey(
            'fk_tiebreak_rule_type',
            '{{%tiebreak_rule}}',
            'tiebreak_type_id',
            '{{%tiebreak_type}}',
            'id',
            'CASCADE'
            );
    }
    
    public function safeDown()
    {
        try {
            $this->dropForeignKey('fk_tiebreak_rule_tournament', '{{%tiebreak_rule}}');
        } catch (\yii\db\Exception $e) {
            // Ignorieren, falls der Foreign Key gar nicht existiert
        }
        try {
            $this->dropForeignKey('fk_tiebreak_rule_type', '{{%tiebreak_rule}}');
        } catch (\yii\db\Exception $e) {
            // Ignorieren, falls der Foreign Key gar nicht existiert
        }
        $this->dropTable('{{%tiebreak_rule}}');
    }
}