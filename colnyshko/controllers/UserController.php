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
use app\models\user_related\ImageRelation;


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

        $categories = $model->getCategories()->all();
        $category = (object) ['id' => 0, 'name' => 'Все', 'images' => $model->getImages()->all()];


        array_unshift($collections, $allCollection);
        array_unshift($categories, $category);

        $images = ImageRelation::getImagesByCriteria($model->id);

        return $this->render('view', [
            'model' => $model,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
            'collections' => $collections,
            'collection' => $allCollection,
            'categories' => $categories,
            'category' => $category,
            'images' => $images,
            'isMain' => true,

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

        // Получение коллекций пользователя с сортировкой по ID в порядке убывания
        $collections = $user->getCollections()->orderBy(['id' => SORT_DESC])->all();
        $allCollection = (object) ['id' => 0, 'name' => 'Все', 'images' => $user->getImages()->all()];

        //$categories = $user->getCategories()->all();
        $categories = $user->getCategoriesForCollection($id);
        $category = null;
        if (!empty($categories)){
            $category = (object) ['id' => 0, 'name' => 'Все', 'images' => $user->getImages()->all()];
        }

        array_unshift($collections, $allCollection);
        array_unshift($categories, $category);

        $images = ImageRelation::getImagesByCriteria($user->id, $id);

        return $this->render('view', [
            'model' => $user,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
            'collections' => $collections,
            'collection' => $collection,
            'categories' => $categories,
            'category' => $category,
            'images' => $images,
            'isMain' => false,
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

        $model->deleteLocalFile($uploadRecord->file_name);

        // Обновляем статус файла в базе данных
        $uploadRecord->status = 'cloud_uploaded';
        $uploadRecord->cloud_url = $cloudUrl;
        $uploadRecord->save();

        return [
            'success' => true,
            'file_id' => $uploadRecord->file_name,
            'cloud_url' => $cloudUrl,
        ];
    }

    public function actionDeleteFromCloud()
    {
        $fileUrl = Yii::$app->request->post('fileUrl');

        // Получите запись файла по URL
        $fileRecord = Upload::findOne(['cloud_url' => $fileUrl]);

        if (!$fileRecord) {
            return $this->asJson(['success' => false, 'error' => 'Файл не найден.']);
        }

        // Проверьте, совпадает ли user_id с ID текущего пользователя
        if ($fileRecord->user_id != Yii::$app->user->id) {
            return $this->asJson(['success' => false, 'error' => 'Недостаточно прав для удаления этого файла.']);
        }

        $model = new UploadForm();
        $result = $model->deleteObjectFromCloud($fileUrl);

        if ($result !== false) {
            // Если вы хотите вернуть подробную информацию о результате удаления:
            return $this->asJson(['success' => true, 'cloud_response' => $result->toArray()]);

        } else {
            return $this->asJson(['success' => false, 'error' => 'Не удалось удалить файл.']);
        }
    }

    public function actionDeleteLocalFile()
    {
        $fileUrl = Yii::$app->request->post('fileUrl');

        $fileRecord = Upload::findOne(['file_name' => $fileUrl]);

        if (!$fileRecord) {
            return $this->asJson(['success' => false, 'error' => 'Файл не найден.']);
        }

        if ($fileRecord->user_id != Yii::$app->user->id) {
            return $this->asJson(['success' => false, 'error' => 'Недостаточно прав для удаления этого файла.']);
        }

        $fileRecord->is_canceled = 1;
        $fileRecord->save();

        return $this->asJson(['success' => true]);
    }
    public function actionGetCollections() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Получение ID текущего пользователя
        $userId = Yii::$app->user->identity->id;

        // Запрос коллекций пользователя с сортировкой по ID в порядке убывания
        $collections = Collection::find()->where(['user_id' => $userId])->orderBy(['id' => SORT_DESC])->all();

        // Формирование ответа
        $result = [['id' => 0, 'name' => 'Все']];  // По умолчанию "Все"
        foreach ($collections as $collection) {
            $result[] = ['id' => $collection->id, 'name' => $collection->name];
        }

        return ['success' => true, 'data' => $result];
    }

    public function actionGetCategories() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Получение ID текущего пользователя
        $userId = Yii::$app->user->identity->id;

        // Запрос категорий пользователя с сортировкой по ID в порядке убывания
        $categories = Category::find()->where(['user_id' => $userId])->orderBy(['id' => SORT_DESC])->all();

        // Формирование ответа
        $result = [['id' => 0, 'name' => 'Все']];  // По умолчанию "Все"
        foreach ($categories as $category) {
            $result[] = ['id' => $category->id, 'name' => $category->name];
        }

        return ['success' => true, 'data' => $result];
    }
    public function actionCreateCategory()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new Category();
        $model->user_id = Yii::$app->user->identity->id;
        $model->name = Yii::$app->request->post('name');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Категория успешно создана.',
                'newCategory' => [
                    'id' => $model->id,
                    'name' => $model->name
                ]
            ];
        } else {
            return ['success' => false, 'message' => 'Не удалось создать категорию.'];
        }
    }

    public function actionCategory($username, $id)
    {
        $user = User::find()->where(['username' => $username])->one();

        if ($user === null) {
            throw new NotFoundHttpException("The user was not found.");
        }

        $category = Category::find()->where(['id' => $id, 'user_id' => $user->id])->one();

        if ($category === null) {
            throw new NotFoundHttpException("The category was not found.");
        }

        $this->view->title = "Категория " . Html::encode($category->name) . " пользователя " . Html::encode($user->display_name);

        $currentUser = Yii::$app->user->identity;
        $isSubscribed = Subscription::find()->where(['user_id' => $user->id, 'subscriber_id' => $currentUser->id])->exists();

        // Получение коллекций пользователя с сортировкой по ID в порядке убывания
        //$collections = $user->getCollections()->orderBy(['id' => SORT_DESC])->all();
        $collections = $user->getCollectionsForCategory($id);


        $allCollection = (object) ['id' => 0, 'name' => 'Все', 'images' => $user->getImages()->all()];

        $categories = $user->getCategories()->all();
        $allCategory = (object) ['id' => 0, 'name' => 'Все', 'images' => $user->getImages()->all()];

        array_unshift($collections, $allCollection);
        array_unshift($categories, $allCategory);

        $images = ImageRelation::getImagesByCriteria($user->id, null, $id);

        return $this->render('view', [
            'model' => $user,
            'currentUser' => $currentUser,
            'isSubscribed' => $isSubscribed,
            'collections' => $collections,
            'collection' => $allCollection,
            'categories' => $categories,
            'category' => $category,
            'images' => $images,
            'isMain' => false,
        ]);
    }

    public function actionAddPostcard()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $currentUser = Yii::$app->user->identity;
            if (Yii::$app->request->isPost) {
                $title = Yii::$app->request->post('title');
                $description = Yii::$app->request->post('description');
                $imageURL = Yii::$app->request->post('imageURL');
                $collection = Yii::$app->request->post('collection');
                $category = Yii::$app->request->post('category');

                // Создаем запись изображения
                $image = Image::createNew($imageURL, $description, $currentUser->id);
                if (!$image) {
                    return ['success' => false, 'message' => 'Ошибка при сохранении изображения.'];
                }

                // Создаем запись для связи
                $relation = ImageRelation::createNew($image->id, $collection, $category, $title, $description, $currentUser->id);

                if (!$relation) {
                    return ['success' => false, 'message' => 'Ошибка при добавлении связи изображения.'];
                }
                $currentUser->updateLastUpdated($collection, $category);
                return ['success' => true, 'message' => 'Открытка успешно добавлена!'];
            } else {
                return ['success' => false, 'message' => 'Недопустимый тип запроса.'];
            }
        }else {
            return ['success' => false, 'message' => 'Вы не авторизованы. Пожалуйста, авторизуйтесь сначала.'];
        }
    }

    public function actionPostCardData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->request->isPost) {
                $id = Yii::$app->request->post('id');

                $imageURL = ImageRelation::getImageURLById($id);

                if (!$imageURL) {
                    return ['success' => false, 'message' => 'Изображение не найдено.'];
                }

                return ['success' => true, 'message' => 'Данные открытки успешно получены!', 'imageURL' => $imageURL];
            } else {
                return ['success' => false, 'message' => 'Недопустимый тип запроса.'];
            }
        } else {
            return ['success' => false, 'message' => 'Вы не авторизованы. Пожалуйста, авторизуйтесь сначала.'];
        }
    }
    public function actionSavePostcard()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $currentUser = Yii::$app->user->identity;
            if (Yii::$app->request->isPost) {
                $title = Yii::$app->request->post('title');
                $description = Yii::$app->request->post('description');
                $imageURL = Yii::$app->request->post('imageURL');
                $collection = Yii::$app->request->post('collection');
                $category = Yii::$app->request->post('category');

                // Найти существующую запись изображения
                $image = Image::findByUrl($imageURL);
                if (!$image) {
                    return ['success' => false, 'message' => 'Изображение не найдено.'];
                }

                // Создаем запись для связи
                $relation = ImageRelation::createNew($image->id, $collection, $category, $title, $description, $currentUser->id);
                if (!$relation) {
                    return ['success' => false, 'message' => 'Ошибка при добавлении связи изображения.'];
                }
                $currentUser->updateLastUpdated($collection, $category);
                return ['success' => true, 'message' => 'Открытка успешно сохранена!'];
            } else {
                return ['success' => false, 'message' => 'Недопустимый тип запроса.'];
            }
        } else {
            return ['success' => false, 'message' => 'Вы не авторизованы. Пожалуйста, авторизуйтесь сначала.'];
        }
    }

    public function actionMovePostcard() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Вы не авторизованы. Пожалуйста, авторизуйтесь сначала.'];
        }

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Недопустимый тип запроса.'];
        }

        $currentUser = Yii::$app->user->identity;

        $collectionId = Yii::$app->request->post('collection');
        $categoryId = Yii::$app->request->post('category');
        $cardId = Yii::$app->request->post('cardId');

        // Проверка принадлежности картинки пользователю
        $relation = ImageRelation::find()->where(['id' => $cardId, 'user_id' => $currentUser->id])->one();

        if (!$relation) {
            return ['success' => false, 'message' => 'Доступ запрещен или изображение не найдено.'];
        }
        $currentUser->updateLastUpdated($collectionId, $categoryId);

        // Обновление коллекции и категории
        if ($relation->updateCollectionAndCategory($collectionId, $categoryId)) {
            return ['success' => true, 'message' => 'Открытка успешно перемещена!'];
        } else {
            return ['success' => false, 'message' => 'Ошибка при перемещении открытки.'];
        }
    }
    public function actionDeletePostcard() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Вы не авторизованы. Пожалуйста, авторизуйтесь сначала.'];
        }

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Недопустимый тип запроса.'];
        }

        $currentUser = Yii::$app->user->identity;
        $cardId = Yii::$app->request->post('cardId');

        $relation = ImageRelation::find()->where(['id' => $cardId, 'user_id' => $currentUser->id])->one();

        if (!$relation) {
            return ['success' => false,'message' => 'Доступ запрещен или изображение не найдено.'];
        }

        if ($relation->markAsDeleted()) {
            return [
                'success' => true,
                'message' => 'Открытка успешно удалена!',
                'cardId' => $cardId];
        } else {
            return ['success' => false, 'message' => 'Ошибка при удалении открытки.'];
        }
    }
}
