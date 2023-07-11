<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use app\assets\LoginFormWidgetAsset;

class LoginModalWidget extends Widget
{
    public $model;

    public function run()
    {
        ob_start();

        Modal::begin([
            'title' => 'Войти на Солнышко',
            'id' => 'login-modal',
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
        ]);

        $form = ActiveForm::begin([
            'id' => 'login-form',
            'action' => ['/auth'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => false,
        ]);

        echo $form->field($this->model, 'email')->textInput(['autofocus' => true]);
        echo $form->field($this->model, 'password')->passwordInput();
        echo Html::tag('div',
            Html::a('Забыли пароль?', '#', [
                'id' => 'forgot',
                'data-bs-dismiss' => 'modal',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#restore-modal'
            ]) .
            Html::submitButton('Продолжить', ['class' => 'btn btn-primary', 'name' => 'login-button']),
            ['class' => 'modal-footer']
        );

        ActiveForm::end();

        echo '<hr class="my-2">';

        echo Html::tag('div',
            Html::tag('span','Ещё не зарегистрированы? ',  ['class' => 'js-btn', 'data-link' => 'register']).
            Html::a('Регистрация', '#', [
                'class' => 'js-btn',
                'data-bs-dismiss' => 'modal',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#register-modal'
            ])
            , ['class' => 'not-registered-wrapper']);

        Modal::end();

        LoginFormWidgetAsset::register($this->view);

        return ob_get_clean();
    }

}
