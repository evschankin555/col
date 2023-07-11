<?php
/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Войти на Солнышко';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'action' => ['/auth'],
    'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
    'enableAjaxValidation' => false,
]); ?>

<?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Введите ваш адрес электронной почты']) ?>
<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите ваш пароль']) ?>

<div class="form-group">
    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div>
    <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>
    <br>
    <?= Html::a('Регистрация', ['site/signup']) ?>
</div>
