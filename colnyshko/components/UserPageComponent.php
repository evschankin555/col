<?php
namespace app\components;

use yii\base\Component;
use yii\helpers\Html;
class UserPageComponent extends Component
{
    public $model;
    public $collections;
    public $collection;
    public $currentUser;
    public $isSubscribed;

    public function renderUserCard()
    {
        $output = '<div class="card border-secondary mb-3">
            <div class="card-header card-title text-center">
                <a id="user-nick" class="card-title" href="/' . Html::encode($this->model->username) . '">
                    ' . Html::encode($this->model->display_name) . '</a>
            </div>
            <div class="card-body text-center">
                <img id="user-avatar" class="d-block user-select-none"
                     src="' . $this->model->getAvatarUrl() . '" width="100%" height="100%"
                     alt="">
                <div class="user-username">
                    <h3 id="user-login">@' . Html::encode($this->model->username) . '</h3>
                </div>
                <div class="form-group buttons">';

        if ($this->currentUser && $this->currentUser->id == $this->model->id) {
            $output .= '<button type="button" class="btn btn-info btn-sm"  data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="' . htmlspecialchars(\app\models\Tooltip::getTooltip('createCategory', 'ru')->message) . '">
                            Создать категорию
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="addPostcardButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="' . htmlspecialchars(\app\models\Tooltip::getTooltip('createCard', 'ru')->message) . '">
                            Добавить открытку
                        </button>';
        } else {
            if ($this->isSubscribed) {
                $output .= '<button id="unsubscribe-btn" class="btn btn-secondary btn-sm" data-username="' . Html::encode($this->model->username) . '">Отписаться</button>';
            } else {
                $output .= '<button id="subscribe-btn" class="btn btn-danger btn-sm" data-username="' . Html::encode($this->model->username) . '">Подписаться</button>';
            }
        }

        $output .= '</div>
            </div>
            <div class="card-footer statistics">
                <small><strong>Подписчиков:</strong> <span class="subscribersCount">' . $this->model->getFormattedSubscribersCount() . '</span></small>
                <small><strong>Подписок:</strong> <span class="subscriptionsCount">' . $this->model->getFormattedSubscriptionsCount() . '</span></small>
            </div>
        </div>';

        return $output;
    }

    public function renderCollectionsList()
    {
        $output = '<div class="card border-info mb-3">
            <div id="collections-list" class="card-body">';

        foreach ($this->collections as $collectionItem) {
            if ($collectionItem->id === 0) {
                $output .= '<a class="btn btn-' . ($this->collection->id == $collectionItem->id ? 'primary' : 'secondary') . ' btn-sm" href="/' . $this->model->username . '" title="' . Html::encode($collectionItem->name) . '">
                            ' . Html::encode($collectionItem->name) . '<span class="badge bg-secondary">' . count($collectionItem->images) . '</span>
                        </a>';
            } else {
                $output .= '<a class="btn btn-' . ($this->collection->id == $collectionItem->id ? 'primary' : 'secondary') . ' btn-sm" href="/' . $this->model->username . '/collection/' . $collectionItem->id . '" title="' . Html::encode($collectionItem->name) . '">
                            ' . Html::encode($collectionItem->name) . '<span class="badge bg-secondary">' . count($collectionItem->images) . '</span>
                        </a>';
            }
        }

        $output .= '</div>';

        if ($this->currentUser && $this->currentUser->id == $this->model->id) {
            if ($this->collection->id === 0) {
                $output .= '<button type="button" data-username="' . Html::encode($this->model->username) . '" class="btn btn-secondary btn-sm" id="createCollectionButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="' . htmlspecialchars(\app\models\Tooltip::getTooltip('createCollection', 'ru')->message) . '">
                            Создать коллекцию
                        </button>';
            } else {
                $tooltipMessage = \app\models\Tooltip::getTooltip('deleteCollection', 'ru') ? htmlspecialchars(\app\models\Tooltip::getTooltip('deleteCollection', 'ru')->message) : '';
                $output .= '<button type="button" data-username="' . Html::encode($this->model->username) . '" data-collection-id="' . $this->collection->id . '" class="btn btn-secondary btn-sm" id="deleteCollectionButton" data-bs-toggle="popover" data-bs-placement="right" data-bs-html="true" data-bs-content="' . $tooltipMessage . '">
                            Удалить коллекцию
                        </button>';
            }
        }

        $output .= '</div>';

        return $output;
    }

    public function renderCreateCollectionModal()
    {
        $output = '<div class="modal fade" id="createCollection" tabindex="-1" aria-labelledby="createCollectionModalLabel" aria-hidden="true">
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
</div>';

        return $output;
    }

    public function renderDeleteCollectionModal()
    {
        $output = '<div class="modal fade" id="deleteCollection" tabindex="-1" aria-labelledby="deleteCollectionModalLabel" aria-hidden="true">
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
</div>';

        return $output;
    }

    public function renderAddPostcardModal()
    {
        $output = '<div class="modal fade" id="addPostcard" tabindex="-1" aria-labelledby="addPostcardModalLabel" aria-hidden="true">
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
                                    <input id="gtfs_upload" name="file" type="file" class="myform__file-upload" accept=".jpg, .jpeg, .png, .gif, .mp4, .webp" hidden>
                                
                                </div>
                                <div class="file-upload-container-process">
                                  <div class="label-file-upload-container">
                                    <div class="title-status"><span class="icon-upload"></span>Загрузить открытку...</div>
                                    <span class="block-download-link__part">
                                                <span class="block-download-link__icon zip"></span>
                                                <div class="block-download-link__column">
                                                    <span class="block-download-links__title file-info">improved_gtfs.zip</span>
                                                    <span class="block-download-links__size file-info-sub"></span>
                                                </div>
                                                <button id="del_file_upload" type="button" class="btn-close" aria-label="Close"></button>

                                            </span>
                 
                                    <div class="upload-progress-bar">
                                        <div class="upload-progress" style="width: 0%;"></div>
                                    </div>
                                    <input type="hidden" id="uploaded_file_id" name="uploaded_file_id" value="">
                                    <div class="error-file"></div>
                                </div>
                                  
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
</div>';

        return $output;
    }

}
