<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%flags}}`.
 */
class m250212_190457_create_flags_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%flags}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(10)->notNull(),
            'name_de' => $this->string(255)->notNull(),
            'name_en' => $this->string(255)->notNull(),
            'name_fr' => $this->string(255)->notNull(),
            'startdatum' => $this->date(),
            'enddatum' => $this->date()->defaultValue(null),
            'flag_url' => $this->string(500)->notNull(),
        ]);
        
        // Indexe für schnellere Abfragen
        $this->createIndex('unique_flags', 'flags', ['key', 'startdatum', 'enddatum'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%flags}}');
    }
}
