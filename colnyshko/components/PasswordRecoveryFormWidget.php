<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\web\View;

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

        $js = <<<JS
            $('form#restore-form').on('beforeSubmit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: 'post',
                    data: form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            $("#restore-modal .error-text").html(response.error).show();
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
