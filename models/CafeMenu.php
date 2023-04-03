<?php

namespace app\models;

use yii\db\ActiveRecord;

class CafeMenu extends ActiveRecord
{
    public static function tableName()
    {
        return 'cafe_menu';
    }

    public function rules()
    {
        return [
            [['name', 'price', 'ingredients'], 'required'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['ingredients'], 'string'],
        ];
    }
}
