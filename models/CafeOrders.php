<?php

namespace app\models;

use yii\db\ActiveRecord;

class CafeOrder extends ActiveRecord
{
    public static function tableName()
    {
        return 'cafe_orders';
    }

    public function rules()
    {
        return [
            [['total_price'], 'required'],
            [['total_price'], 'number'],
        ];
    }

    public function getOrderDishes()
    {
        return $this->hasMany(CafeOrderDish::class, ['order_id' => 'id']);
    }
}
