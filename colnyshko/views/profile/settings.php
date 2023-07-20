<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Настройки профиля';
?>
<style>
    .form-group.buttons{
        display: flex;
        justify-content: end;
    }

    #avatarModal .img-thumbnail {
        padding: 0.5rem;
        background-color: rgba(255,255,255,.2);
    }
    #avatarModal .row {
        --bs-gutter-y: 1rem;
</style>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <img class="d-block user-select-none" src="<?= $model->getAvatarUrl() ?>" width="100%" height="200" alt="">
                <h5 id="user-nick" class="card-title">
                    <a id="user-nick" class="card-title" href="http://localhost/<?= Html::encode($model->username) ?>">
                        <?= Html::encode($model->display_name) ?></a>
                </h5>                <?= Html::button('Выбрать аватар', ['class' => 'btn btn-info', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#avatarModal']) ?>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Выберите аватар</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <?php for ($i = 1; $i <= 16; $i++): ?>
                                <div class="col-3">
                                    <img src="/svg/account-avatar-profile-user-<?= $i ?>-svgrepo-com.svg" class="img-thumbnail avatar-option" data-avatar-id="<?= $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php
        $this->registerJs(<<<JS
    $('.avatar-option').click(function () {
        let avatarId = $(this).data('avatar-id');
        let avatarUrl = '/svg/account-avatar-profile-user-' + avatarId + '-svgrepo-com.svg';
        
        // Отправить AJAX POST-запрос на сервер для обновления аватара
        $.ajax({
            url: '/profile/update-avatar',
            type: 'POST',
            data: { avatar: avatarUrl },
            success: function() {
                // Обновить изображение аватара на странице
                $('.user-select-none').attr('src', avatarUrl);
                
                // Закрыть модальное окно
                $('#avatarModal').modal('hide');
            }
        });
    });
JS);

        ?>
    </div>
    <div class="col-md-8">
        <div class="card border-info mb-3">
            <div class="card-header">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => ['class' => 'form-horizontal', 'data-ajax' => '1'],
                    'enableAjaxValidation' => false,
                ]); ?>

                <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
                <?= $form->field($model, 'display_name')->textInput(['autofocus' => true, 'placeholder' => 'Введите ваше отображаемое имя'])->label('Отображаемое имя') ?>
                <?= $form->field($model, 'avatar')->hiddenInput(['id' => 'user-avatar'])->label(false) ?>
                <?= $form->field($model, 'description')->textarea(['placeholder' => 'О себе'])->label('О себе') ?>

                <div class="form-group buttons">
                    <?= Html::submitButton('Обновить профиль', ['class' => 'btn btn-info', 'name' => 'update-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>
