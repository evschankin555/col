<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 64],
            [['created_at'], 'safe'],
            [['is_confirmed'], 'boolean'],
            [['confirmation_code'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
            ['email', 'unique', 'message' => 'This email is already taken.'],
            ['email', 'email', 'message' => 'Invalid email format.'],
        ];
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

    // Функция для авторизации
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

}
