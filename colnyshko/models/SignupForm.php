<?php
namespace app\models;
use app\models\User;
use yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $agreement;
    public $display_name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'agreement'], 'required'],
            ['username', 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Данный электронный адрес уже зарегистрирован.'],
            ['password', 'string', 'min' => 6],
            ['agreement', 'boolean'],
            ['agreement', 'compare', 'compareValue' => 1, 'message' => 'Вы должны принять условия соглашения.'],
            ['display_name', 'required'],
            ['display_name', 'string', 'max' => 255]
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->agreement = $this->agreement;
        $user->display_name = $this->display_name;

        // Если пользователь был успешно сохранен и ему было отправлено подтверждение по электронной почте,
        // мы авторизуем его в системе.
        if ($user->save() && $user->sendConfirmationEmail()) {
            Yii::$app->user->login($user);
            return true;
        }
        return false;
    }

}
