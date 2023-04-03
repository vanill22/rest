<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cafe_order_dishes}}`.
 */
class m230403_111647_create_cafe_order_dishes_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('cafe_order_dishes', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'dish_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_cafe_order_dishes_order_id',
            'cafe_order_dishes',
            'order_id',
            'cafe_orders',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_cafe_order_dishes_dish_id',
            'cafe_order_dishes',
            'dish_id',
            'cafe_menu',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_cafe_order_dishes_order_id', 'cafe_order_dishes');
        $this->dropForeignKey('fk_cafe_order_dishes_dish_id', 'cafe_order_dishes');
        $this->dropTable('cafe_order_dishes');
    }
}
