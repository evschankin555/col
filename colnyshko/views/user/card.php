<?php

use app\components\user\UserImagesWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\assets\UserPageAsset;
UserPageAsset::register($this);
?>
<div class="card card-page border-info mb-2">
    <div id="card-container" class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <div class="card-container-image">
                        <img src="<?=$image->url?>" alt="Uploaded Image">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" style="position: relative; display: block;">
                    <h2 for="postcard-title" ...=""><?=$imageRelation->title?></h2>
                </div>
                <div class="mb-3" style="position: relative; display: block;">
                    <label for="postcard-description" ...=""><?=$imageRelation->description?></label>
                </div>
            </div>
        </div>
    </div>
</div>
