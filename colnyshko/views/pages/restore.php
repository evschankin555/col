<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Восстановление пароля';
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

    .help-block{
        padding: 15px 0 0px 0;
    }
    .field-passwordrecoveryform-email{
        margin-bottom: 20px;
    }
</style>

<div class="card border-info mb-3">
    <div class="card-header">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'restore-form',
            'action' => ['/restore'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => false,
        ]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Введите ваш адрес электронной почты']) ?>

        <div class="form-group buttons">
            <div>
                <?= Html::a('Назад', ['login'], ['class' => 'card-link']) ?>
            </div>
            <?= Html::submitButton('Восстановить', ['class' => 'btn btn-info', 'name' => 'restore-button']) ?>

        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
