<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cafe_orders}}`.
 */
class m230403_111634_create_cafe_orders_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('cafe_orders', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'total_price' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('cafe_orders');
    }
}
