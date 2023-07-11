<?php
namespace app\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

class UserMenuButtonWidget extends Widget
{
    public function run()
    {
        echo Html::button(Yii::$app->user->identity->username, [
            'class' => 'btn btn-primary',
            'type' => 'button',
            'data-bs-toggle' => 'offcanvas',
            'data-bs-target' => '#offcanvasUserMenu',
            'aria-controls' => 'offcanvasUserMenu'
        ]);
    }
}
