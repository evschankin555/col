<?php
namespace app\models;

use app\models\user_related\Collection;
use app\models\user_related\Image;
use app\models\user_related\ImageRelation;
use app\models\user_related\Category;
use Yii;
use app\components\TimedActiveRecord;
use yii\web\IdentityInterface;
class User extends TimedActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'password', 'agreement'], 'required', 'message' => 'Это поле необходимо заполнить.'],
            [['username', 'email', 'avatar'], 'string', 'max' => 255],  // добавлено поле 'avatar'
            [['password'], 'string', 'max' => 64],
            [['created_at'], 'safe'],
            [['is_confirmed'], 'boolean'],
            [['confirmation_code'], 'string', 'max' => 255],
            [['email'], 'email', 'message' => 'Неверный формат электронной почты.'],
            [['email'], 'unique', 'message' => 'Этот адрес электронной почты уже используется.'],
            ['display_name', 'required'],
            ['display_name', 'string', 'max' => 255],
            [['last_updated_collection_id', 'last_updated_category_id'], 'integer'],
            [['last_updated_collection_id'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['last_updated_collection_id' => 'id']],
            [['last_updated_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['last_updated_category_id' => 'id']],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Отображаемое имя',
            'email' => 'Eмейл',
            'password' => 'Пароль',
            'agreement' => 'Согласие на обработку персональных данных',
            'avatar' => 'Аватар',
            'last_updated_collection_id' => 'Последняя обновленная коллекция',
            'last_updated_category_id' => 'Последняя обновленная категория',

        ];
    }

    public function getAvatarUrl()
    {
        // Если у пользователя есть аватар, то возвращаем путь до него, иначе возвращаем путь до дефолтной картинки
        return !empty($this->avatar) ? $this->avatar : Yii::getAlias('@web') . '/svg/account-avatar-profile-user-14-svgrepo-com.svg';
    }



    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Не используется
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        // Не используется
    }

    public function validateAuthKey($authKey)
    {
        // Не используется
    }

    public function register($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->setPassword($password);
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->is_confirmed = 0;
        $this->confirmation_code = Yii::$app->security->generateRandomString();

        if ($this->validate() && $this->save()) {
            $this->sendConfirmationEmail();
            return true;
        }
        return $this->errors;
    }

    public function sendConfirmationEmail()
    {
        $emailSender = new \app\models\EmailSender();
        return $emailSender->sendConfirmationEmail($this->email, $this->confirmation_code);
    }


    public function confirmEmail($confirmationCode)
    {
        $user = static::findOne(['confirmation_code' => $confirmationCode]);
        if ($user !== null) {
            $user->is_confirmed = 1;
            $user->confirmation_code = '';
            if ($user->save()) {
                return $user;
            }
        }
        return null;
    }

    public function login($email, $password)
    {
        $user = static::findOne(['email' => $email]);
        if ($user !== null /*&& $user->is_confirmed */ && $user->validatePassword($password)) {
            return Yii::$app->user->login($user);
        }
        return false;
    }


    // Функция для выхода
    public function logout()
    {
        return Yii::$app->user->logout();
    }

    // Функции для установки и проверки пароля
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public function restore($email)
    {
        $user = static::findOne(['email' => $email]);

        if ($user === null) {
            return false;
        }

        // Генерация случайного кода для восстановления пароля
        $user->confirmation_code = Yii::$app->security->generateRandomString(32);

        // Сохранение кода подтверждения в базе данных
        if (!$user->save()) {
            return false;
        }

        // Отправка письма с инструкциями по восстановлению пароля
        return $this->sendRestoreEmail($user);
    }
    protected function sendRestoreEmail($user)
    {
        $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['en/reset-password', 'token' => $user->confirmation_code]);

        return Yii::$app->mailer->compose(['html' => 'restorePassword-html'], ['user' => $user, 'resetLink' => $resetLink])
            ->setFrom([Yii::$app->params['supportEmail'] => 'GTFS.pro'])
            ->setTo($user->email)
            ->setSubject('Password reset for GTFS.pro')
            ->send();
    }
    public function findUserByResetCode($code)
    {
        return static::findOne(['confirmation_code' => $code]);
    }

    public function getFormattedSubscribersCount()
    {
        $subscribersCount = Subscription::find()->where(['user_id' => $this->id])->count();
        if ($subscribersCount >= 1000000) {
            return Yii::$app->formatter->asDecimal($subscribersCount / 1000000, 3) . 'М';
        }
        else if ($subscribersCount >= 1000) {
            return Yii::$app->formatter->asDecimal($subscribersCount, 0);
        }
        else {
            return $subscribersCount;
        }
    }

    public function getFormattedSubscribersCountCard()
    {
        $subscribersCount = Subscription::find()->where(['user_id' => $this->id])->count();

        if ($subscribersCount == 0) {
            return '0 подписчиков';
        }

        // Функция для форматирования чисел с учетом русского языка
        $formatNumber = function ($number) {
            $lastDigit = $number % 10;
            $lastTwoDigits = $number % 100;

            if ($lastDigit == 1 && $lastTwoDigits != 11) {
                return "{$number} подписчик";
            } elseif (in_array($lastDigit, [2, 3, 4]) && !in_array($lastTwoDigits, [12, 13, 14])) {
                return "{$number} подписчика";
            } else {
                return "{$number} подписчиков";
            }
        };

        if ($subscribersCount >= 1000000) {
            $formattedCount = Yii::$app->formatter->asDecimal($subscribersCount / 1000000, 3);
            return "{$formattedCount}М подписчиков";
        } elseif ($subscribersCount >= 1000) {
            $formattedCount = Yii::$app->formatter->asDecimal($subscribersCount / 1000, 1);
            return "{$formattedCount}К подписчиков";
        } else {
            return $formatNumber($subscribersCount);
        }
    }

    public function getFormattedSubscriptionsCount()
    {
        $subscriptionsCount = Subscription::find()->where(['subscriber_id' => $this->id])->count();
        if ($subscriptionsCount >= 1000000) {
            return Yii::$app->formatter->asDecimal($subscriptionsCount / 1000000, 3) . 'М';
        }
        else if ($subscriptionsCount >= 1000) {
            return Yii::$app->formatter->asDecimal($subscriptionsCount, 0);
        }
        else {
            return $subscriptionsCount;
        }
    }
    public function actionGetSubscribersCount($username)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = User::find()->where(['username' => $username])->one();

        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        return ['success' => true, 'count' => $user->getFormattedSubscribersCount()];
    }

    public function actionGetSubscriptionsCount($username)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = User::find()->where(['username' => $username])->one();

        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден.'];
        }

        return ['success' => true, 'count' => $user->getFormattedSubscriptionsCount()];
    }
    public function getCollections()
    {
        return $this->hasMany(Collection::className(), ['user_id' => 'id']);
    }
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])->via('collections');
    }
    public function getCategories()
    {
        return $this->hasMany(\app\models\user_related\Category::className(), ['user_id' => 'id']);
    }

    public function getCollectionsForCategory($categoryId)
    {
        return Collection::getCollectionsForCategory($this->id, $categoryId);
    }

    public function getCategoriesForCollection($collectionId)
    {
        return Category::getCategoriesForCollection($this->id, $collectionId);
    }

    public function updateLastUpdatedCollection($collectionId)
    {
        $this->last_updated_collection_id = $collectionId;
        return $this->save();
    }

    public function updateLastUpdatedCategory($categoryId)
    {
        $this->last_updated_category_id = $categoryId;
        return $this->save();
    }

    public function updateLastUpdated($collectionId = null, $categoryId = null)
    {
        if ($collectionId !== null) {
            $this->last_updated_collection_id = $collectionId;
        }

        if ($categoryId !== null) {
            $this->last_updated_category_id = $categoryId;
        }

        return $this->save();
    }


}
