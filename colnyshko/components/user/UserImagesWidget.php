<?php
namespace app\components\user;

use app\assets\ImagesWidgetDropdownModalAsset;
use yii\base\Widget;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use Yii;

class UserImagesWidget extends Widget
{
    public $images;
    public $currentUser;
    public $isCurrentUserOwner;

    public function run()
    {
        $this->currentUser = Yii::$app->user->identity;
        $output = '<div class="grid user-images">';
        foreach ($this->images as $image) {
            $output .= '<div class="grid-item" id="grid-item-'.$image->id.'">';
            $output .= $this->renderCard($image);
            $output .= '</div>';
        }
        $output .= '</div>';

        $output .= $this->renderCreateCollectionModal();
        $output .= $this->renderDeleteCollectionModal();
        $output .= $this->renderAddPostcardModal();
        $output .= $this->renderCreateCategoryModal();
        return $output;
    }

    private function renderCard($imageRelation)
    {
        $this->isCurrentUserOwner = ($imageRelation->user_id == ($this->currentUser ? $this->currentUser->id : null));
        $image = $imageRelation->image;

        $output = '<div class="card mb-2 js-card">';
        $output .= '<div class="card-body media-card-body">';

        $url = $image->url;
        $src = $url;
        $output .= '<div>';
        if ($this->currentUser) {
            // Проверка, является ли текущий пользователь владельцем изображения
            $isImageOwner = ($imageRelation->user_id == ($this->currentUser ? $this->currentUser->id : null));

            if ($isImageOwner) {
                // Пользователь может редактировать только свои изображения
                $output .= $this->renderButton('Переместить...', 'btn-info', 'move-button', $image, $imageRelation);
                $output .= $this->renderButton('Удалить...', 'btn-danger', 'del-button', $image, $imageRelation);
            }

            // Все авторизованные пользователи могут сохранять изображения
            $output .= $this->renderButton('Сохранить...', 'btn-warning', 'save-button', $image, $imageRelation);
        }

        $output .= '<img class="image-modal" data-html="HTML код..." data-bb="BB код..."  data-src="' . $image->url . '" src="' . $src . '" alt="' . $image->description . '" data-href="'.$image->href.'">';
        $output .= $this->renderDropdown($image);
        $output .= '</div>';

        $output .= '</div>';
        $output .= '
                 <div class="user-username-images">
                     <a class="user-login-images" href="/'.$imageRelation->username
            .'">@' . Html::encode($imageRelation->username) . '</a>
                 </div>';
        $output .= '</div>';
        $output .= $this->renderModal();
        $output .= $this->renderAlert();
        ImagesWidgetDropdownModalAsset::register($this->view);

        return $output;
    }


    private function renderDropdown($image)
    {
        $url = $image->href;
        $output = '<ul class="nav nav-pills">';
        $output .= '<li class="nav-item dropdown images-menu">';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<li class="nav-item dropdown images-menu">';
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Отправить</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<a class="dropdown-item ok" rel="nofollow noopener" target="_blank" href="https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl='.$url.'">Одноклассники</a>';
        $output .= '<a class="dropdown-item vk" rel="nofollow noopener" target="_blank" href="https://vk.com/share.php?url='.$url.'">Вконтакте</a>';
        $output .= '<a class="dropdown-item mm" rel="nofollow noopener" target="_blank"  href="https://connect.mail.ru/share?url='.$url.'">Мой мир</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item whatsapp" rel="nofollow noopener" target="_blank" href="https://api.whatsapp.com/send?text='.$url.'">
                                WhatsUp</a>';
        $output .= '<a class="dropdown-item telegram" rel="nofollow noopener" target="_blank" href="https://telegram.me/share/url?url='.$url.'">Телеграм</a>';
        $output .= '<a class="dropdown-item viber" rel="nofollow noopener" target="_blank" href="viber://forward?text='.$url.'">Вайбер</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item link" href="#">Ссылка</a>';
        $output .= '<a class="dropdown-item html" href="#">HTML</a>';
        //$output .= '<a class="dropdown-item bb" href="#">BB-code</a>';
        $output .= '</div>';
        $output .= '</li>';
        $output .= '</ul>';
        return $output;
    }
    private function renderModal()
    {
        ob_start();
        Modal::begin([
            'title' => '<span id="modal-title"></span>',
            'id' => 'myModal',
            'footer' => Html::button('Копировать', ['class' => 'btn btn-primary images-copy']). Html::button('Close', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']),
            'dialogOptions' => ['class' => 'modal-dialog-centered'],
        ]);
        echo '<span id="modal-content">Содержимое модального окна...</span>';
        Modal::end();
        return ob_get_clean();
    }
    private function renderAlert()
    {

        return '
<div id="copy-toast" class="toast alert-success" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header">
    <strong class="me-auto">Выполнено!</strong>
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    Скопировано в буфер обмена.
  </div>
</div>

';
    }

    public function renderButton($text, $btnClass, $actionButtonClass, $image, $imageRelation)
    {
        if ($text === "Переместить..." && !$this->isCurrentUserOwner) {
            return '';
        }
        $output = '    <button type="button" ';
        $output .= 'class="btn ' . $btnClass . ' btn-sm ' . $actionButtonClass . '" ';
        $output .= 'data-id="' . $imageRelation->id . '" ';
        $output .= 'data-src="' . $image->url . '" ';
        $output .= 'data-title="' . htmlspecialchars($imageRelation->title) . '" ';
        $output .= 'data-description="' . htmlspecialchars($imageRelation->description) . '" ';
        $output .= 'data-collection-id="' . $imageRelation->collection_id . '" ';
        $output .= 'data-category-id="' . $imageRelation->category_id . '" ';
        $output .= '>';
        $output .= $text;
        $output .= '    </button>';

        return $output;
    }

    private function renderCreateCollectionModal()
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
                <button type="button" class="btn btn-warning" id="create-collection-btn">Создать</button>
            </div>
        </div>
    </div>
</div>';

        return $output;
    }
    private function renderDeleteCollectionModal()
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
    private function renderAddPostcardModal()
    {
        $output = '<div class="modal fade" id="addPostcard" tabindex="-1" aria-labelledby="addPostcardModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPostcardModalLabel">Добавить открытку</h5>
                <button type="button" class="btn-close" id="closeModalButtonAddPostcard" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <div class="file-upload-container-image">
                                   <button id="del_file_upload-image" type="button" class="btn-close" aria-label="Close"></button>
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
                           <div class="mb-3" style="position: relative;"> 
    <label for="postcard-title" ...>Название</label>
    <input type="text" id="postcard-title" class="form-control" placeholder="Добавить название">
    <div class="counter">0/100</div> 
</div>
<div class="mb-3" style="position: relative;">
    <label for="postcard-description" ...>Описание</label>
    <textarea id="postcard-description" class="form-control" rows="6" placeholder="Добавить описание"></textarea>
    <div class="counter">0/1000</div> 
</div>
    <input type="hidden" id="input_file_upload-image" value="">



<div class="collection-wrapper">
    <div class="btn-group collection-buttons" role="group" aria-label="Button group with nested dropdown">
        <button id="collectionButton" type="button" class="btn btn-primary btn-lg" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-value="0">Коллекция: Все</button>
                <div class="btn-group" role="group">
            <button id="dropdownCollectionButton" type="button" class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <div class="dropdown-menu" aria-labelledby="dropdownCollectionButton">
                <a id="createCollectionButtonAddCard" class="dropdown-item" data-value="evening">Создать коллекцию</a>
                <div class="dropdown-divider"></div>
            </div>
        </div>
    </div>
<!-- Скрытая мини-форма для создания коллекции -->
<div class="new-collection-form d-none input-group">
    <input type="text" class="form-control" placeholder="Название коллекции" aria-label="Название коллекции">
    <button id="createCollectionButtonForm" class="btn btn-warning btn-sm" type="button">Создать</button>
    <button id="cancelCollectionButton" class="btn btn-secondary btn-sm" type="button">Отмена</button>
</div>




</div>
<div class="category-wrapper">
    <div class="btn-group category-buttons" role="group" aria-label="Button group with nested dropdown">
        <button id="categoryButton" type="button" class="btn btn-info btn-lg" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-value="0">Категория: Все</button>
        
        <div class="btn-group" role="group">
            <button id="dropdownCategoryButton" type="button" class="btn btn-info btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            
            <div class="dropdown-menu" aria-labelledby="dropdownCategoryButton">
                <a id="createCategoryButtonAddCard" class="dropdown-item" data-value="evening">Создать категорию</a>
                <div class="dropdown-divider"></div>
            </div>
        </div>
    </div>
<!-- Скрытая мини-форма для создания категории -->
<div class="new-category-form d-none input-group">
    <input type="text" class="form-control" placeholder="Название категории" aria-label="Название категории">
    <button id="createCategoryButtonForm" class="btn btn-warning btn-sm" type="button">Создать</button>
    <button id="cancelCategoryButton" class="btn btn-secondary btn-sm" type="button">Отмена</button>
</div>

</div>
<div class="full-screen-container">
  <div class="message-deleting" id="message-deleting">
      Вы действительно хотите удалить открытку?
  </div>
</div>
<div class="error-messages">
    <ul id="error-list" class="list-group"></ul>
</div>

<input type="hidden" name="card-id">


                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="save-postcard-btn">Добавить</button>
            </div>
        </div>
    </div>
</div>';

        return $output;
    }
    private function renderCreateCategoryModal()
    {
        $output = '<div class="modal fade" id="createCategory" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Создать категорию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="new-category-name" class="form-control" placeholder="Название категории">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="create-category-btn">Создать</button>
            </div>
        </div>
    </div>
</div>';

        return $output;
    }
}

