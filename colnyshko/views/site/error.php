<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = 'Страница не найдена (404)';
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="alert alert-danger">
        Здесь ничего нет, пожалуйста <a href="/">вернитесь на Главную</a>
    <span><?=$message?></span>
</div>

