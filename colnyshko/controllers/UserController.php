<?php

namespace app\controllers;

use Yii;
use app\models\ProfileForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Subscription;
use app\models\user_related\Collection;
use app\models\user_related\Category;
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

        $collections = $model->getCollections()->all();
        $allCollection = (object) ['id' => 0, 'name' => 'Все', 'images' => $model->getImages()->all()];

        array_unshift($collections, $allCollection);

        return $this->render('view', [
            'model' => $model,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
            'collections' => $collections,
            'collection' => $allCollection,
        ]);
    }

    public function actionCollection($username, $id)
    {
        $user = User::find()->where(['username' => $username])->one();

        if ($user === null) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $collection = Collection::find()->where(['id' => $id, 'user_id' => $user->id])->one();

        if ($collection === null) {
            throw new NotFoundHttpException("The collection was not found.");
        }

        $this->view->title = "Коллекция " . Html::encode($collection->name) . " пользователя " . Html::encode($user->display_name);

        $currentUser = Yii::$app->user->identity;
        $isSubscribed = Subscription::find()->where(['user_id' => $user->id, 'subscriber_id' => $currentUser->id])->exists();
        $collections = $user->getCollections()->all();
        $allCollection = (object) ['id' => 0, 'name' => 'Все', 'images' => $user->getImages()->all()];

        array_unshift($collections, $allCollection);

        return $this->render('view', [
            'model' => $user,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
            'collections' => $collections,
            'collection' => $collection,
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

    public function actionCreateCollection()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new Collection();
        $model->user_id = Yii::$app->user->identity->id;
        $model->name = Yii::$app->request->post('name');

        if ($model->save()) {
            return ['success' => true, 'message' => 'Коллекция успешно создана.'];
        } else {
            return ['success' => false, 'message' => 'Не удалось создать коллекцию.'];
        }
    }

}
