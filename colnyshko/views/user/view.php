<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Ваш профиль';
?>
<style>
    .form-group.buttons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        align-items: center;
    }
    .card-footer.statistics {
        display: flex;
        justify-content: left;
        flex-wrap: wrap;
        align-items: center;
    }

    #avatarModal .img-thumbnail {
        padding: 0.5rem;
        background-color: rgba(255, 255, 255, .2);
    }

    #avatarModal .row {
        --bs-gutter-y: 1rem;
    }

    .form-group.buttons a:not(:last-child) {
        margin-bottom: 0.5rem;
    }
    #header-subscribes{
        padding: 15px 10px;
    }
    .card.border-secondary.mb-3{
        margin-bottom: 0.75rem!important;
    }
    #my-groups{
        border-radius: 6px 6px 0 0;
    }
    #user-avatar{
        margin-bottom: 20px;
    }
</style>



<div class="row">
    <div class="col-md-3">
        <div class="card border-secondary mb-3">
            <div class="card-header card-title text-center">
                    <a id="user-nick" class="card-title" href="http://localhost/<?= Html::encode($model->username) ?>">
                        <?= Html::encode($model->display_name) ?></a>
            </div>
            <div class="card-body text-center">
                <img id="user-avatar" class="d-block user-select-none" src="<?= $model->getAvatarUrl() ?>" width="100%" height="200"
                     alt="">

                <div class="form-group buttons">
                    <?= Html::a('Создать категорию', ['category/create'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?= Html::a('Создать открытку', ['card/create'], ['class' => 'btn btn-info btn-sm']) ?>
                    <?= Html::a('Подписаться', ['category/create'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>


            </div>
            <div class="card-footer statistics">
                <small> <strong>Подписчиков:</strong> 234 456</small>
                <small> <strong>Коллекций:</strong> 1 234</small>
                <small> <strong>Категорий:</strong> 234</small>
                <small> <strong>Открыток:</strong> 234 456</small>
            </div>
        </div>

        <div class="card border-secondary mb-3">
        </div>



    </div>
    <div class="col-md-9">
        <div class="card border-info mb-3">
            <div class="card-body">
                <!-- Тут будет контент пользователя -->
            </div>
        </div>
    </div>

</div>
