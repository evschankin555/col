<?php
namespace app\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

class UserMenuButtonWidget extends Widget
{
    public function run()
    {
        $username = Html::encode(Yii::$app->user->identity->username);
        $avatarUrl = Html::encode(Yii::$app->user->identity->avatarUrl);

        // Форматируем содержимое кнопки, чтобы включить аватар
        $buttonContent = Html::img($avatarUrl, [
            'class' => 'avatar-in-menu user-select-none',
            'width' => '30', // Задайте размеры, которые вам подходят
            'height' => '30', // Задайте размеры, которые вам подходят
        ]);
        $buttonContent .= Html::encode($username);

        return Html::button($buttonContent, [
            'type' => 'button',
            'class' => 'btn btn-primary button-avatar-in-menu',
            'data-bs-toggle' => 'offcanvas',
            'data-bs-target' => '#offcanvasUserMenu',
            'aria-controls' => 'offcanvasUserMenu',
        ]);
    }
}
