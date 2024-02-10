<?php

use app\components\user\UserImagesWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\assets\UserPageAsset;
UserPageAsset::register($this);
$countSubscribers = $userCard->getFormattedSubscribersCountCard();
?>
<div class="card border-info mb-2" style="max-width: 450px!important;">
    <div id="card-container" class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 card-title">
                    <h2 class="postcard-title"><?=$imageRelation->title?></h2>
                </div>


                <div class="mb-3">
                    <div class="card-container-image">
                        <img src="<?=$image->url?>" alt="Uploaded Image">
                        <div class="user-username-images">
                            <a  href="/<?=$imageRelation->username?>">@<?=$imageRelation->username?></a>
                            <span><?=$countSubscribers?></span>
                            <a type="button" class="btn btn-warning btn-sm">Подписаться</a>
                        </div>
                    </div>
                </div>
                <div class="mb-3 card-desc">
                    <span><?=$imageRelation->description?></span>
                </div>
                <div class="mb-3 btns">
                    <?php if ($collection): ?>
                        <a type="button" class="btn btn-outline-primary btn-sm" title='Коллекция "<?=$collection->name?>"'><?=$collection->name?></a>
                    <?php endif; ?>

                    <?php if ($category): ?>
                        <a type="button" class="btn btn-outline-info btn-sm" title='Категория "<?=$category->name?>"'><?=$category->name?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
