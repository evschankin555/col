<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class LoginButtonWidget extends Widget
{
    public function run()
    {
        // Вместо вызова модального окна здесь просто возвращаем кнопку
        $output = Html::button('Войти', ['class' => 'btn btn-primary', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#login-modal']);
        return $output;
    }
}
