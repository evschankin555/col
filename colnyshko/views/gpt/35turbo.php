<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\YourModel */

$this->title = 'GPT 3.5 Turbo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gpt-35turbo-page">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <textarea rows="30" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <div id="response" rows="30" class="form-control"></div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <button id="send-button" type="button" class="btn btn-success">Отправить</button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-info">Копировать</button>
        </div>
    </div>
</div>
<script>
    window.onload = function() {
        $(document).ready(function() {
            // Обработчик клика на кнопку "Отправить"
            $('#send-button').click(function() {
                // Получите содержимое первого textarea
                var textareaContent = $('.col-md-6:first textarea').val();

                $.ajax({
                    url: '/gpt/35turbo-send',
                    type: 'POST',
                    data: { content: textareaContent },
                    success: function(response) {
                        // Обновите содержимое второго textarea с ответом
                        $('#response').text(response.generatedText.choices[0].message.content);
                        console.log(response.generatedText.choices[0].message.content);
                    },
                    error: function(error) {
                        // Обработка ошибок при отправке данных
                        console.error('Произошла ошибка при отправке данных:', error);
                    }
                });
            });
        });
    };
</script>
