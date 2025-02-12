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
            'key' => $this->string(10)->notNull()->unique(),
            'name_de' => $this->string(255)->notNull(),
            'name_en' => $this->string(255)->notNull(),
            'name_fr' => $this->string(255)->notNull(),
            'startdatum' => $this->date(),
            'enddatum' => $this->date()->defaultValue(null),
            'flag_url' => $this->string(500)->notNull(),
            'parent_key' => $this->string(10)->defaultValue(null),
            'ioc' => $this->string(3)->defaultValue(null),
            'anmerkung' => $this->text()->defaultValue(null),
        ]);
        
        // Indexe für schnellere Abfragen
        $this->createIndex('idx-flags-key', 'flags', 'key', true);
        $this->createIndex('idx-flags-parent_key', 'flags', 'parent_key');
        $this->createIndex('idx-flags-ioc', 'flags', 'ioc');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%flags}}');
    }
}
