<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Nav;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Button;
use GuzzleHttp\Client;
use yii\caching\FileCache;
use app\components\CategoryWidget;
use app\components\ImagesWidget;
use app\components\PaginationWidget;

$this->title = 'Солнышко';
?>

<div class="jumbotron">
    <h1>Кодировка в Base64+</h1>
</div>

<div class="row">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="encoder">
                    <div class="mb-3">
                        <label for="input" class="form-label">Введите текст для кодирования</label>
                        <textarea class="form-control" id="input">Пример текста</textarea>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" id="toBase64">Кодировать в Base64</button>
                    </div>
                    <div class="mb-3">
                        <label for="base64output" class="form-label">Результат Base64</label>
                        <textarea class="form-control" id="base64output" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" id="toBase642">Кодировать в Base64:2</button>
                    </div>
                    <div class="mb-3">
                        <label for="base642output" class="form-label">Результат Base64:2</label>
                        <textarea class="form-control" id="base642output" readonly></textarea>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="decoder">
                    <div class="mb-3">
                        <label for="base642input" class="form-label">Введите текст для декодирования</label>
                        <textarea class="form-control" id="base642input"></textarea>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-info" id="fromBase642">Декодировать из Base64:2</button>
                    </div>
                    <div class="mb-3">
                        <label for="base64decoded" class="form-label">Результат Base64</label>
                        <textarea class="form-control" id="base64decoded" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-info" id="fromBase64">Декодировать из Base64</button>
                    </div>
                    <div class="mb-3">
                        <label for="decodedoutput" class="form-label">Результат декодирования</label>
                        <textarea class="form-control" id="decodedoutput" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    jQuery(document).ready(function() {
        const base64alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

        // Создаем алфавит Base64:2 из пар символов Base64.
        let base642alphabet = [];
        for (let i = 0; i < base64alphabet.length; i++) {
            for (let j = 0; j < base64alphabet.length; j++) {
                base642alphabet.push(base64alphabet[i] + base64alphabet[j]);
            }
        }

        function toBase642(base64) {
            let result = '';
            let padding = 0;
            for (let i = 0; i < base64.length; i += 2) {
                if (base64[i] === "=" || base64[i+1] === "=") {
                    padding++;
                    continue; // пропустить символы заполнения
                }
                const pair = base64[i] + (base64[i+1] || '');
                const index = base642alphabet.indexOf(pair);
                if (index === -1) {
                    continue; // пропустить неизвестные пары
                }
                result += base64alphabet[Math.floor(index / 64)] + base64alphabet[index % 64];
            }
            return 'Base64:2:' + result + (padding ? '==' + padding : '');
        }

        function fromBase642(base642) {
            // Этот код будет более сложным, так как нам нужно обратно преобразовать данные.
            // Это потребует дополнительной логики и, возможно, использования библиотек для обработки base64.
        }

        $('#toBase64').click(function() {
            var input = $('#input').val();
            var output = btoa(unescape(encodeURIComponent(input)));
            $('#base64output').val(output);
        });

        $('#fromBase64').click(function() {
            var input = $('#base64decoded').val();
            var output = decodeURIComponent(escape(atob(input)));
            $('#decodedoutput').val(output);
        });

        $('#toBase642').click(function() {
            var input = $('#base64output').val();
            var output = toBase642(input);
            $('#base642output').val(output);
        });

        $('#fromBase642').click(function() {
            var input = $('#base642input').val();
            var output = fromBase642(input);
            $('#base64decoded').val(output);
        });
    });



</script>