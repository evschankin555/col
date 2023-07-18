<?php
namespace app\models;

use Yii;
use yii\base\Model;

class PasswordRecoveryForm extends Model
{
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required', 'message' => 'Это поле необходимо заполнить.'],
            ['email', 'email', 'message' => 'Неверный формат электронной почты.'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['is_confirmed' => 1],
                'message' => 'Пользователя с таким адресом электронной почты не существует.'
            ],
        ];
    }

    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'is_confirmed' => 1,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        return $user->restore($this->email);
    }
}
