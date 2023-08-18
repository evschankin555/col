<?php

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
    </div>
</div>
<?= $userPageComponent->renderCreateCollectionModal(); ?>
<?= $userPageComponent->renderDeleteCollectionModal(); ?>
<?= $userPageComponent->renderAddPostcardModal(); ?>
<?= $userPageComponent->renderCreateCategoryModal(); ?>