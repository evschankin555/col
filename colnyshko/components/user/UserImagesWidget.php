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

        return $output;
    }

    private function renderCard($imageRelation)
    {

        $this->isCurrentUserOwner = ($imageRelation->user_id == $this->currentUser->id);
        $image = $imageRelation->image;

        $output = '<div class="card mb-2 js-card">';
        $output .= '<div class="card-body media-card-body">';

        $url = $image->url;
        $src = $url;
        $output .= '<div>';
        $output .= $this->renderButton('Сохранить...', 'btn-warning', 'save-button', $image, $imageRelation);
        $output .= $this->renderButton('Переместить...', 'btn-info', 'move-button', $image, $imageRelation);
        $output .= $this->renderButton('Удалить...', 'btn-danger', 'del-button', $image, $imageRelation);

        $output .= '<img class="image-modal" data-html="HTML код..." data-bb="BB код..."  data-src="' . $image->url . '" src="' . $src . '" alt="' . $image->description . '" data-href="'.$image->href.'">';
        $output .= $this->renderDropdown($image);
        $output .= '</div>';

        $output .= '</div>';
        /*$output .= '<div class="card-footer">';
        $output .= '</div>';*/
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

}

