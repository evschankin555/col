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

    #collections-list.card-body {
        flex: 1 1 auto;
        color: var(--bs-card-color);
        line-height: 2.1;
        text-align: center;
        padding: 1rem;
    }
    #createCollectionButton, #deleteCollectionButton{

        width: 150px;
        margin: 0 10px 10px;
    }
    #addPostcard .modal-dialog{
        max-width: 900px;
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
                        <button type="button" class="btn btn-info btn-sm"  data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCategory', 'ru')->message); ?>">
                            Создать категорию
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="addPostcardButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCard', 'ru')->message); ?>">
                            Добавить открытку
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
            <div id="collections-list" class="card-body">
                <?php foreach($collections as $collectionItem): ?>
                    <?php if($collectionItem->id === 0): ?>
                        <a class="btn btn-<?= $collection->id == $collectionItem->id ? 'primary' : 'secondary' ?> btn-sm" href="/<?= $model->username ?>" title="<?= Html::encode($collectionItem->name) ?>">
                            <?= Html::encode($collectionItem->name) ?><span class="badge bg-secondary"><?= count($collectionItem->images) ?></span>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-<?= $collection->id == $collectionItem->id ? 'primary' : 'secondary' ?> btn-sm" href="/<?= $model->username ?>/collection/<?= $collectionItem->id ?>" title="<?= Html::encode($collectionItem->name) ?>">
                            <?= Html::encode($collectionItem->name) ?><span class="badge bg-secondary"><?= count($collectionItem->images) ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php if ($currentUser && $currentUser->id == $model->id): ?>
                <?php if ($collection->id === 0): ?>
                    <button type="button" data-username="<?= Html::encode($model->username) ?>" class="btn btn-secondary btn-sm" id="createCollectionButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= htmlspecialchars(\app\models\Tooltip::getTooltip('createCollection', 'ru')->message); ?>">
                        Создать коллекцию
                    </button>
                <?php else: ?>
                    <button type="button" data-username="<?= Html::encode($model->username) ?>" data-collection-id="<?= $collection->id ?>" class="btn btn-secondary btn-sm" id="deleteCollectionButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="<?= ($tooltip = \app\models\Tooltip::getTooltip('deleteCollection', 'ru')) ? htmlspecialchars($tooltip->message) : ''; ?>">
                        Удалить коллекцию
                    </button>

                <?php endif; ?>
            <?php endif; ?>
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
<!-- добавим скрытое поле username в модальное окно -->
<div class="modal fade" id="deleteCollection" tabindex="-1" aria-labelledby="deleteCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCollectionModalLabel">Удалить коллекцию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить эту коллекцию?
                <input type="hidden" id="delete-collection-id" value="">
                <input type="hidden" id="delete-collection-username" value=""> <!-- username для редиректа -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Удалить</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addPostcard" tabindex="-1" aria-labelledby="addPostcardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPostcardModalLabel">Добавить открытку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postcard-upload-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-row__input-container upload-container">
                                <div class="file-upload-container">
                                    <span class="icon-upload"></span>
                                    <div class="file-upload-text">
                                        Перетащите изображение<br /> <a id="browse-link">Загрузить файл...</a>
                                    </div>
                                    <div class="file-upload-subtext">
                                        Советуем использовать файлы до 20 мб <br />в формате jpg, png, gif, mp4, webp
                                    </div>
                                    <input id="gtfs_upload" name="file" type="file" class="myform__file-upload" accept=".zip" hidden>
                                </div>
                                <div class="label-file-upload-container">
                                    <div class="title-status"><span class="icon-upload"></span>Загрузить открытку...</div>
                                    <span class="block-download-link__part">
                                                <span class="block-download-link__icon zip"></span>
                                                <div class="block-download-link__column">
                                                    <span class="block-download-links__title file-info">improved_gtfs.zip</span>
                                                    <span class="block-download-links__size file-info-sub"></span>
                                                </div>
                                            </span>
                                    <span id="del_file_upload" class="popup__close" style="display: none;"></span>
                                    <div class="upload-progress-bar">
                                        <div class="upload-progress" style="width: 0%;"></div>
                                    </div>
                                    <input type="hidden" id="uploaded_file_id" name="uploaded_file_id" value="">
                                    <div class="error-file"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="postcard-title" class="form-label">Название</label>
                                <input type="text" id="postcard-title" class="form-control" placeholder="Добавить название">
                            </div>
                            <div class="mb-3">
                                <label for="postcard-description" class="form-label">Описание</label>
                                <textarea id="postcard-description" class="form-control" rows="3" placeholder="Добавить описание"></textarea>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-postcard-btn">Добавить</button>
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
let createCollectionButton = document.getElementById('createCollectionButton');
if (createCollectionButton !== null) {
    createCollectionButton.addEventListener('click', function () {
        myModal = new bootstrap.Modal(document.getElementById('createCollection'), {});
        myModal.show();
    });
}

$('#create-collection-btn').click(function() {
    let collectionName = $('#new-collection-name').val();
    let username = $('#createCollectionButton').data('username');  

    $.ajax({
        url: '/user/create-collection',
        type: 'POST',
        data: { name: collectionName },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);

                // Добавьте новую коллекцию в список
                let newCollection = $('<a></a>')
                    .attr('href', '/' + username + '/collection/' + data.newCollection.id) 
                    .addClass('btn btn-secondary btn-sm')
                    .attr('title', data.newCollection.name)
                    .text(data.newCollection.name)
                    .append('<span class="badge bg-secondary">0</span>');
                /*$('#collections-list').append(newCollection);*/
                newCollection.insertAfter('#collections-list a:last-child');
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
   
    $('#new-collection-name').val('');
    $('#createCollection').modal('hide');
});
$('#deleteCollectionButton').click(function() {
    let collectionId = $(this).data('collection-id');
    let username = $(this).data('username');
    $('#delete-collection-id').val(collectionId);
    $('#delete-collection-username').val(username); 

    let myModal = new bootstrap.Modal(document.getElementById('deleteCollection'), {});
    myModal.show();
});

$('#confirm-delete-btn').click(function() {
    let collectionId = $('#delete-collection-id').val();
    let username = $('#delete-collection-username').val(); 

    $.ajax({
        url: '/user/delete-collection',
        type: 'POST',
        data: { id: collectionId },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
                // Удалите коллекцию из списка
                $('#' + collectionId).remove();
                setTimeout(function() {
                    window.location.href = "/" + username;
                }, 1000);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteCollection'));
    deleteModal.hide();
});

$('#addPostcardButton').click(function() {
    $('#addPostcard').modal('show');
});

$('#save-postcard-btn').click(function() {
    let postcardTitle = $('#postcard-title').val();
    let postcardDescription = $('#postcard-description').val();
    let postcardImage = $('#postcard-image').prop('files')[0];  

    let formData = new FormData();
    formData.append('title', postcardTitle);
    formData.append('description', postcardDescription);
    formData.append('image', postcardImage);

    $.ajax({
        url: '/user/add-postcard',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
                // Здесь можно добавить действия после успешной загрузки, например, обновление списка открыток на странице
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
   
    $('#postcard-title').val('');
    $('#postcard-description').val('');
    $('#postcard-image').val('');
    $('#addPostcard').modal('hide');
});


JS;
$this->registerJs($script);

?>
