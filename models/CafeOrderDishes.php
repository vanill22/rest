<?php

namespace app\models;

use yii\db\ActiveRecord;

class CafeOrderDish extends ActiveRecord
{
    public static function tableName()
    {
        return 'cafe_order_dishes';
    }

    public function rules()
    {
        return [
            [['order_id', 'dish_id', 'quantity'], 'required'],
            [['order_id', 'dish_id', 'quantity'], 'integer'],
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(CafeOrder::class, ['id' => 'order_id']);
    }

    public function getDish()
    {
        return $this->hasOne(CafeMenu::class, ['id' => 'dish_id']);
    }
}