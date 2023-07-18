<?php
/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Регистрация';
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
        align-items: center;
    }
</style>
<div class="card border-info mb-3">
    <div class="card-header">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'signup-form',
            'action' => ['/signup'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => false,
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Введите ваше имя'])->label('Ваше имя или Никнейм') ?>
        <?= $form->field($model, 'email')->textInput(['placeholder' => 'Введите ваш адрес электронной почты'])->label('Электронная почта') ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите ваш пароль', 'value' => ''])->label('Пароль') ?>
        <?= $form->field($model, 'agreement')->checkbox()->label('Согласен с условиями') ?>

        <div class="form-group buttons">
            <div>
                <?= Html::a('Назад', ['login'], ['class' => 'card-link']) ?>
            </div>
            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

