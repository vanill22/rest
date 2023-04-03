<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cafe_menu}}`.
 */
class m230403_111611_create_cafe_menu_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('cafe_menu', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'ingredients' => $this->text()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('cafe_menu');
    }
}
