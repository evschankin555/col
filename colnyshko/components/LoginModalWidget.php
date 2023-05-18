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
        $output = '';

        ob_start();
        Modal::begin([
            'title' => 'Войти на Солнышко',
            'id' => 'login-modal',
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
        ]);
        $modal = ob_get_clean();

        ob_start();
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
        $formOutput = ob_get_clean();

        $output .= $modal;
        $output .= $formOutput;
        $output .= '<hr class="my-2">';

        $output .= Html::tag('div',
            Html::tag('span','Ещё не зарегистрированы? ',  ['class' => 'js-btn', 'data-link' => 'register']).
            Html::a('Регистрация', '#', [
                'class' => 'js-btn',
                'data-bs-dismiss' => 'modal',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#register-modal'
            ])
            , ['class' => 'not-registered-wrapper']);


        ob_start();
        Modal::end();
        $output .= ob_get_clean();

        LoginFormWidgetAsset::register($this->view);

        return $output;
    }
}
