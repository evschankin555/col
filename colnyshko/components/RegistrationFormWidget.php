<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\web\View;

class RegistrationFormWidget extends Widget
{
    public $model;

    public function run()
    {
        ob_start();

        Modal::begin([
            'title' => 'Регистрация на Солнышко',
            'id' => 'register-modal',
            'dialogOptions' => ['class' => 'modal-dialog-centered modal-lg'],
        ]);

        $form = ActiveForm::begin([
            'id' => 'register-form',
            'action' => ['/register'],
            'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
            'enableAjaxValidation' => true,
        ]); ?>

        <?= $form->field($this->model, 'username')->textInput() ?>

        <?= $form->field($this->model, 'email')->textInput() ?>

        <?= $form->field($this->model, 'password')->passwordInput() ?>

        <?= $form->field($this->model, 'agreement')->checkbox() ?>

        <div class="form-group">
            <?= Html::tag('div',
                Html::a('Вернуться', '#', [
                    'data-bs-dismiss' => 'modal',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#login-modal'
                ])
                .
                Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'register-button'])
                ,
                ['class' => 'modal-footer']
            );
            ?>
        </div>

        <?php ActiveForm::end();

        Modal::end();

        $js = <<<JS
            $('form#register-form').on('beforeSubmit', function(e) {
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: 'post',
                    data: form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            $("#register-modal .error-text").html(response.error).show();
                        }
                    },
                    error: function () {
                        alert("Something went wrong. Please try again.");
                    }
                });
                return false;
            });
        JS;
        $this->view->registerJs($js, View::POS_READY);

        return ob_get_clean();
    }
}
