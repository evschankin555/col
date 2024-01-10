<?php

use app\components\user\UserImagesWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\assets\UserPageAsset;
UserPageAsset::register($this);
use app\components\UserPageComponent;

$userPageComponent = new UserPageComponent([
    'model' => $model,
    'collections' => $collections,
    'collection' => $collection,
    'currentUser' => $currentUser,
    'isSubscribed' => $isSubscribed,
    'categories' => $categories,
    'category' => $category,
    'images' => $images,
    'isMain' => $isMain,
]);

?>
<div class="row">
    <div class="col-md-3">
        <?= $userPageComponent->renderUserCard();?>
        <div class="card border-secondary mb-3">
        </div>
    </div>
    <div class="col-md-9">
        <?= $userPageComponent->renderCollectionsList();?>
        <?= UserImagesWidget::widget(['images' => $images]);?>
    </div>
</div>