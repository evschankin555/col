<?php
/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Войти';
?>
<style>
    .main-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(80vh - 80px);
    }

    .card {
        margin-top: 2rem;
        margin-bottom: 2rem;
        width: 350px;
    }
    .form-group.buttons{
        display: flex;
        justify-content: space-between;
    }

</style>
<div class="card border-info mb-3">
    <div class="card-header">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'action' => ['/login'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => false,
        ]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Введите ваш адрес электронной почты'])->label('Электронная почта') ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите ваш пароль'])->label('Пароль') ?>


        <div class="form-group buttons">
            <div>
                <?= Html::a('Забыли пароль?', ['restore'], ['class' => 'card-link']) ?>
                <br>
                <?= Html::a('Регистрация', ['signup'], ['class' => 'card-link']) ?>

            </div>
            <?= Html::submitButton('Войти', ['class' => 'btn btn-info', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
