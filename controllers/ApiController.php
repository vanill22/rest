<?php

namespace app\controllers;

use app\models\CafeMenu;
use app\models\CafeOrder;
use app\models\CafeOrderDish;
use Exception;
use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CafeApiController extends ActiveController
{
    public $modelClass = CafeMenu::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'open-order' => ['POST'],
                'add-dish' => ['POST'],
                'popular-chefs' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function actionOpenOrder()
    {
        $data = Yii::$app->getRequest()->getBodyParams();

        $order = new CafeOrder();
        $order->total_price = 0;

        if (!$order->save()) {
            throw new BadRequestHttpException(Json::encode($order->getErrors()));
        }

        return [
            'order_id' => $order->id,
            'order_url' => Url::to(['/cafe-api/view-order', 'id' => $order->id], true),
        ];

    }

    public function actionAddDish()
    {
        $order_id = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'order_id');
        $dish_id = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'dish_id');
        $quantity = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'quantity', 1);

        $order = CafeOrder::findOne($order_id);

        if (!$order) {
            throw new NotFoundHttpException("Order with id $order_id not found");
        }

        $dish = CafeMenu::findOne($dish_id);

        if (!$dish) {
            throw new NotFoundHttpException("Dish with id $dish_id not found");
        }

        $order_dish = new CafeOrderDish();
        $order_dish->order_id = $order_id;
        $order_dish->dish_id = $dish_id;
        $order_dish->quantity = $quantity;
        $order_dish->price = $dish->price * $quantity;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$order_dish->save()) {
                throw new BadRequestHttpException(Json::encode($order_dish->getErrors()));
            }

            $order->total_price += $order_dish->price;

            if (!$order->save()) {
                throw new BadRequestHttpException(Json::encode($order->getErrors()));
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $response = [
            'order_id' => $order->id,
            'dish_id' => $dish->id,
            'dish_name' => $dish->name,
            'dish_price' => $dish->price,
            'quantity' => $quantity,
            'total_price' => $order->total_price,
            'order_url' => Url::to(['/cafe-api/view-order', 'id' => $order->id], true),
        ];

        return $response;
    }

    public function actionPopularChefs($period = 'week')
    {
        $query = new Query();
        $query->select('chef_id, COUNT(*) as total_orders')
            ->from('cafe_order_dish')
            ->leftJoin('cafe_menu', 'cafe_order_dish.dish_id = cafe_menu.id')
            ->where(['>=', 'cafe_order_dish.created_at', strtotime('-' . $period)])
            ->groupBy('chef_id')
            ->orderBy(['total_orders' => SORT_DESC])
            ->limit(10);

        $rows = $query->all();

        $response = [];

        foreach ($rows as $row) {
            $chef = CafeMenu::find()->where(['chef_id' => $row['chef_id']])->one();
            if ($chef) {
                $response[] = [
                    'chef_name' => $chef->chef_name,
                    'total_orders' => (int)$row['total_orders'],
                ];
            }
        }

        return $response;
    }
}
