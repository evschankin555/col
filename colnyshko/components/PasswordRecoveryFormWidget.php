<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\web\View;
use app\assets\PasswordRecoveryFormWidgetAsset;

class PasswordRecoveryFormWidget extends Widget
{
    public $model;

    public function run()
    {
        ob_start();

        Modal::begin([
            'title' => 'Восстановление пароля',
            'id' => 'restore-modal',
            'dialogOptions' => ['class' => 'modal-dialog-centered modal-lg'],
        ]);

        $form = ActiveForm::begin([
            'id' => 'restore-form',
            'action' => ['/restore'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => true,
        ]); ?>

        <?= $form->field($this->model, 'email')->textInput() ?>

        <div class="form-group">
            <?= Html::tag('div',
                Html::a('Вернуться', '#', [
                    'data-bs-dismiss' => 'modal',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#login-modal'
                ])
                .
                Html::submitButton('Восстановить', ['class' => 'btn btn-primary', 'name' => 'restore-button'])
                ,
                ['class' => 'modal-footer']
            );
            ?>
        </div>

        <?php ActiveForm::end();

        Modal::end();
        PasswordRecoveryFormWidgetAsset::register($this->view);
        return ob_get_clean();
    }
}
