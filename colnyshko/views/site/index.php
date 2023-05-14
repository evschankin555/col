<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Nav;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Button;

$this->title = 'Название вашего сайта';
?>


<div class="container">
    <div class="jumbotron">
        <h1 class="display-4">Привет, мир!</h1>
        <p class="lead">Это простой пример элемента с кнопками.</p>
        <hr class="my-4">
        <p>Дополнительная информация.</p>
        <p class="lead">
            <?= Button::widget([
                'label' => 'Действие 1',
                'options' => ['class' => 'btn btn-primary btn-lg'],
            ]) ?>
            <?= Button::widget([
                'label' => 'Действие 2',
                'options' => ['class' => 'btn btn-secondary btn-lg'],
            ]) ?>
        </p>
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
</div>
