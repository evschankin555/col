<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

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

    .form-group.buttons a:not(:last-child), .form-group.buttons button:not(:last-child) {
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
    #user-login{
        font-size: 14px;
        font-weight: 400;
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
                <img id="user-avatar" class="d-block user-select-none"
                     src="<?= $model->getAvatarUrl() ?>" width="100%" height="100%"
                     alt="">
                <div class="user-username">
                    <h3 id="user-login">@<?= Html::encode($model->username) ?></h3>
                </div>
                <div class="form-group buttons">
                    <?php if ($currentUser && $currentUser->id == $model->id): ?>
                        <button type="button" class="btn btn-primary btn-sm" id="createCollectionButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCollection', 'ru')->message); ?>">
                            Создать коллекцию
                        </button>
                        <button type="button" class="btn btn-info btn-sm"  data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCategory', 'ru')->message); ?>">
                            Создать категорию
                        </button>
                        <button type="button" class="btn btn-danger btn-sm"  data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCard', 'ru')->message); ?>">
                            Создать открытку
                        </button>
                    <?php endif; ?>

                    <?php if (!$currentUser || $currentUser->id != $model->id): ?>
                        <?php if ($isSubscribed) : ?>
                            <button id="unsubscribe-btn" class="btn btn-secondary btn-sm" data-username="<?= Html::encode($model->username) ?>">Отписаться</button>
                        <?php else: ?>
                            <button id="subscribe-btn" class="btn btn-danger btn-sm" data-username="<?= Html::encode($model->username) ?>">Подписаться</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>

            <div class="card-footer statistics">
                <small><strong>Подписчиков:</strong> <span class="subscribersCount"><?= $model->getFormattedSubscribersCount() ?></span></small>
                <small><strong>Подписок:</strong> <span class="subscriptionsCount"><?= $model->getFormattedSubscriptionsCount() ?></span></small>
            </div>
        </div>

        <div class="card border-secondary mb-3">
        </div>



    </div>
    <div class="col-md-9">
        <div class="card border-info mb-3">
            <div class="card-body">
                <?php foreach($collections as $collectionItem): ?>
                    <a class="btn btn-secondary btn-sm<?= $collection->id == $collectionItem->id ? ' active' : '' ?>" href="/<?= $model->username ?>/collection/<?= $collectionItem->id ?>" title="<?= Html::encode($collectionItem->name) ?>">
                        <?= Html::encode($collectionItem->name) ?><span class="badge bg-secondary"><?= count($collectionItem->images) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="createCollection" tabindex="-1" aria-labelledby="createCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCollectionModalLabel">Создать коллекцию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="new-collection-name" class="form-control" placeholder="Название коллекции">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="create-collection-btn">Создать</button>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS

$(document).on('click', '#subscribe-btn', function() {
    var button = $(this);
    $.ajax({
        url: '/user/subscribe?username=' + encodeURIComponent(button.data('username')),
        type: 'POST',
        success: function(data) {
            if (data.success) {
                button.text('Отписаться')
                      .attr('id', 'unsubscribe-btn')
                      .toggleClass('btn-secondary btn-danger');
                $('.subscribersCount').text(data.subscribersCount);
                $('.subscriptionsCount').text(data.subscriptionsCount);
                showToast('Вы подписаны.', 'success', 1500);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
    return false;
});

$(document).on('click', '#unsubscribe-btn', function() {
    var button = $(this);
    $.ajax({
        url: '/user/unsubscribe?username=' + encodeURIComponent(button.data('username')),
        type: 'POST',
        success: function(data) {
            if (data.success) {
                button.text('Подписаться')
                      .attr('id', 'subscribe-btn')
                      .toggleClass('btn-danger btn-secondary');
                $('.subscribersCount').text(data.subscribersCount);
                $('.subscriptionsCount').text(data.subscriptionsCount);
                showToast('Вы отписаны.', 'success', 2000);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
    return false;
});
document.getElementById('createCollectionButton').addEventListener('click', function () {
  var myModal = new bootstrap.Modal(document.getElementById('createCollection'), {})
  myModal.show()
})

$('#create-collection-btn').click(function() {
    let collectionName = $('#new-collection-name').val();

    $.ajax({
        url: '/user/create-collection',
        type: 'POST',
        data: { name: collectionName },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    // Очистите поле ввода и закройте модальное окно
    $('#new-collection-name').val('');
    $('#createCollectionModal').modal('hide');
});
JS;
$this->registerJs($script);

?>
