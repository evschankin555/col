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

$this->title = 'Солнышко';
?>

    <div class="jumbotron">
        <?= CategoryWidget::widget(['categories' => $categories])?>
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
