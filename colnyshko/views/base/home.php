<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\assets\UserPageAsset;
UserPageAsset::register($this);
use app\components\HomePageComponent;

$userPageComponent = new HomePageComponent([
    'images' => $images
]);
?>
<div class="row">
    <div class="col-md-3">
        <?= $userPageComponent->renderLeftCard();?>
        <div class="card border-secondary mb-3">
        </div>
    </div>
    <div class="col-md-9">
        <?= $userPageComponent->renderImagesList();?>
    </div>
</div>