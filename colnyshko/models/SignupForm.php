<?php
namespace app\models;
use app\models\User;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $agreement;

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
            ['agreement', 'compare', 'compareValue' => 1, 'message' => 'Вы должны принять условия соглашения.']
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

        return $user->save() && $user->sendConfirmationEmail();
    }
}
