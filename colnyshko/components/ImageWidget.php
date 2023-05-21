<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\Modal;
use app\components\Yii;
use app\assets\ImagesWidgetDropdownModalAsset;

class ImageWidget extends Widget
{
    public $image;
    public function run()
    {
        $output = $this->renderCard($this->image);
        $output .= $this->renderModal();
        $output .= $this->renderAlert();
        ImagesWidgetDropdownModalAsset::register($this->view);

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
    private function renderCard($image)
    {
        $id = md5($image->src);
        $output = '<div class="row cards-images"><div class="card mb-3">';
        $output .= '<div class="card-body media-card-body" data-id="'.$id.'">';

        if ($this->isVideo($image->src)) {
            $output .= '<video class="video-modal" 
            data-alt="' . Html::encode($image->alt) . '" 
            data-src="' . $image->src . '" autoplay loop muted playsinline src="' . $image->src . '" alt="' . $image->alt . '"></video>';
        } else {
            $output .= '<img class="image-modal" data-html="HTML код..." data-bb="BB код..."  data-src="' . $image->src . '" src="' . $image->src . '" alt="' . $image->alt . '">';
        }

        $output .= '<h5 class="card-title">' . Html::encode($image->alt) . '</h5>';
        $output .= '<div id="ok_shareWidget_'.$id.'" class="ok_class"></div>';
        $output .= '</div>';

        $output .= '<div class="card-footer">';
        $output .= $this->renderDropdown($image);
        $output .= '</div>';

        $output .= '</div></div>';
        return $output;
    }
    private function renderDropdown($image)
    {
        $url = $image->href; // Замените на актуальный URL вашего изображения
        $output = '<ul class="nav nav-pills">';

        $output .= '<li class="nav-item dropdown images-menu">';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';

        /*$output .= '<li class="nav-item">';
        $output .= '<a class="nav-link" href="' . $image->href . '">Просмотреть</a>';
        $output .= '</li>';*/

        $output .= '<li class="nav-item dropdown images-menu">';
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Отправить</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<a class="dropdown-item ok"   href="#">Одноклассники</a>';
        $output .= '<a class="dropdown-item vk" href="#">Вконтакте</a>';
        $output .= '<a class="dropdown-item mm" href="#">Мой мир</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item whatsapp" rel="nofollow noopener" target="_blank" href="https://api.whatsapp.com/send?text={{ url }}">
                                WhatsUp</a>';
        $output .= '<a class="dropdown-item telegram" rel="nofollow noopener" target="_blank" href="https://telegram.me/share/url?url={{ url }}">Телеграм</a>';
        $output .= '<a class="dropdown-item viber" rel="nofollow noopener" target="_blank" href="viber://forward?text={{ url }}">Вайбер</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item link" href="#">Ссылка</a>';
        $output .= '<a class="dropdown-item html" href="#">HTML</a>';
        //$output .= '<a class="dropdown-item bb" href="#">BB-code</a>';
        $output .= '</div>';
        $output .= '</li>';


        $output .= '</ul>';

        return $output;
    }
    private function isVideo($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'mp4';
    }
}
