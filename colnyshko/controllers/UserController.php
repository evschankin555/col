<?php

namespace app\controllers;

use Yii;
use app\models\ProfileForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Subscription;
use yii\helpers\Html;

class UserController extends Controller{
    public function actionView($username)
    {
        $model = User::find()->where(['username' => $username])->one();

        if ($model === null) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $this->view->title = "Коллекции пользователя " . Html::encode($model->display_name);

        $currentUser = Yii::$app->user->identity;
        $isSubscribed = Subscription::find()->where(['user_id' => $model->id, 'subscriber_id' => $currentUser->id])->exists();

        return $this->render('view', [
            'model' => $model,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
        ]);
    }
    public function actionSubscribe($username)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = User::find()->where(['username' => $username])->one();

        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        $subscriber = Yii::$app->user->identity;

        $subscription = Subscription::find()->where(['user_id' => $user->id, 'subscriber_id' => $subscriber->id])->one();

        if (!$subscription) {
            $subscription = new Subscription();
            $subscription->user_id = $user->id;
            $subscription->subscriber_id = $subscriber->id;

            if ($subscription->save()) {
                return [
                    'success' => true,
                    'message' => 'Вы подписаны.',
                    'subscribersCount' => $user->getFormattedSubscribersCount(),
                    'subscriptionsCount' => $user->getFormattedSubscriptionsCount()
                ];
            } else {
                return ['success' => false, 'message' => 'Не удалось выполнить подписку.'];
            }
        } else {
            return ['success' => true, 'message' => 'Вы уже подписаны.'];
        }
    }

    public function actionUnsubscribe($username)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = User::find()->where(['username' => $username])->one();

        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        $subscriber = Yii::$app->user->identity;

        $subscription = Subscription::find()->where(['user_id' => $user->id, 'subscriber_id' => $subscriber->id])->one();

        if ($subscription) {
            if ($subscription->delete()) {
                return [
                    'success' => true,
                    'message' => 'Вы отписаны.',
                    'subscribersCount' => $user->getFormattedSubscribersCount(),
                    'subscriptionsCount' => $user->getFormattedSubscriptionsCount()
                ];
            } else {
                return ['success' => false, 'message' => 'Не удалось отписаться.'];
            }
        } else {
            return ['success' => false, 'message' => 'Вы не подписаны на этого пользователя.'];
        }
    }

}
