<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tiebreak_type}}`.
 */
class m250923_033309_create_tiebreak_type_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tiebreak_type}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull()->unique(),
            'description' => $this->string(255)->notNull(),
        ]);
        
        // Standard-Regeln einfügen
        $this->batchInsert('{{%tiebreak_type}}', ['code', 'description'], [
            ['h2h_points', 'Punkte im direkten Vergleich'],
            ['h2h_diff', 'Tordifferenz im direkten Vergleich'],
            ['h2h_goals', 'Tore im direkten Vergleich'],
            ['diff_total', 'Tordifferenz gesamt'],
            ['goals_total', 'Tore gesamt'],
            ['fairplay', 'Fairplaywertung'],
            ['drawing', 'Losentscheid'],
        ]);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%tiebreak_type}}');
    }
}
