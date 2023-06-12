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

?>

<div class="jumbotron">
    <?= CategoryWidget::widget(['categories' => $categories])?>
</div>

<div class="row">
    <?= ImagesWidget::widget([
            'images' => $images,
            'currentCategory' => $currentCategory,
            'currentSubCategory' => $currentSubCategory
    ])?>
    <?= PaginationWidget::widget(['pagination' => $pagination])?>
</div>
