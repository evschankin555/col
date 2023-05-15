<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Nav;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Button;
use GuzzleHttp\Client;
use yii\caching\FileCache;

$this->title = 'Солнышко';
?>

    <div class="jumbotron">
        <h1 class="display-4">Солнышко - открытки, анимации, видео</h1>
        <hr class="my-2">
        <p class="lead main-categories">
            <?php
            foreach ($categories as $category) {
                echo Html::a(
                    $category->name,
                    $category->id == 0 ? '/' : ['site/category', 'id' => $category->id],
                    ['class' => $category->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm']
                );
            }
            ?>
        </p>
        <hr class="my-2">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 1</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 2</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 3</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
    </div>
