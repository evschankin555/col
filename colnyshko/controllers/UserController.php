<?php

namespace app\controllers;

use app\models\user_related\Image;
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
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\UploadForm;
use app\models\Upload;


class UserController extends Controller{
    public function actionView($username)
    {
        $model = User::find()->where(['username' => $username])->one();

        if ($model === null) {
            throw new NotFoundHttpException("Пользователь не найден.");
        }

        $this->view->title = "Коллекции пользователя " . Html::encode($model->display_name);

        $currentUser = Yii::$app->user->identity;
        $isSubscribed = false; // устанавливаем значение по умолчанию

        // Проверяем, авторизован ли пользователь
        if ($currentUser !== null) {
            $isSubscribed = Subscription::find()->where(['user_id' => $model->id, 'subscriber_id' => $currentUser->id])->exists();
        }

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
            return [
                'success' => true,
                'message' => 'Коллекция успешно создана.',
                'newCollection' => [
                    'id' => $model->id,
                    'name' => $model->name
                ]
            ];
        } else {
            return ['success' => false, 'message' => 'Не удалось создать коллекцию.'];
        }
    }
    public function actionDeleteCollection()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $model = Collection::findOne($id);

        if ($model && $model->user_id == Yii::$app->user->identity->id) {
            if ($model->delete()) {
                return ['success' => true, 'message' => 'Коллекция успешно удалена.'];
            } else {
                return ['success' => false, 'message' => 'Не удалось удалить коллекцию.'];
            }
        } else {
            return ['success' => false, 'message' => 'Коллекция не найдена.'];
        }
    }

    public function actionUploadToServer() {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user_id = Yii::$app->user->getId();
        $user = User::findOne($user_id);

        if ($user === null) {
            return ['success' => false, 'error' => 'Пользователь не авторизован.'];
        }

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Неверный метод запроса.'];
        }

        $model = new UploadForm();
        $model->file = UploadedFile::getInstanceByName('file');
        $uploadResponse = $model->upload();

        if (!$uploadResponse || !$uploadResponse['success']) {
            return ['success' => false, 'error' => 'Не удалось загрузить файл.'];
        }

        // Создаем запись в базе данных о загруженном файле
        $uploadRecord = new Upload([
            'user_id' => $user_id,
            'file_name' => $uploadResponse['file_id'],
            'uploaded_at' => date('Y-m-d H:i:s'),
            'status' => 'uploaded',
        ]);

        if (!$uploadRecord->save()) {
            Yii::error('Ошибка при сохранении записи о файле.');
            return ['success' => false, 'error' => 'Ошибка при сохранении данных о файле.'];
        }

        return [
            'success' => true,
            'file_id' => $uploadResponse['file_id']
        ];
    }
    public function actionUploadToCloud() {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user_id = Yii::$app->user->getId();
        $user = User::findOne($user_id);

        if ($user === null) {
            return ['success' => false, 'error' => 'Пользователь не авторизован.'];
        }

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Неверный метод запроса.'];
        }

        $file_id = Yii::$app->request->post('file_id');

        $uploadRecord = Upload::findOne(['file_name' => $file_id]);

        if (!$uploadRecord) {
            return ['success' => false, 'error' => 'Запись о файле не найдена.'];
        }

        $model = new UploadForm();
        $cloudUrl = $model->uploadFileToCloud($user_id, $uploadRecord->file_name);

        if (!$cloudUrl) {
            return ['success' => false, 'error' => 'Ошибка при загрузке файла на облако.'];
        }

        // Обновляем статус файла в базе данных
        $uploadRecord->status = 'cloud_uploaded';
        $uploadRecord->save();

        return [
            'success' => true,
            'file_id' => $uploadRecord->file_name,
            'cloud_url' => $cloudUrl,
        ];
    }



}
