<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\bootstrap5\Modal;
use app\components\Yii;
use app\assets\ImagesWidgetDropdownModalAsset;

class ImagesWidget extends Widget
{
    public $images;

    public function run()
    {
        $output = '<div class="row cards-images">';
        foreach ($this->images as $image) {
            $output .= '<div class="col-md-6">';
            $output .= $this->renderCard($image);
            $output .= '</div>';
        }
        $output .= '</div>';

        $output .= $this->renderModal();
        $output .= $this->renderMediaModal();
        ImagesWidgetDropdownModalAsset::register($this->view);

        return $output;
    }

    private function renderModal()
    {
        ob_start();
        Modal::begin([
            'title' => '<span id="modal-title"></span>',
            'id' => 'myModal',
            'footer' => Html::button('Копировать', ['class' => 'btn btn-info']). Html::button('Close', ['class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']),
            'dialogOptions' => ['class' => 'modal-dialog-centered'],
        ]);
        echo '<span id="modal-content">Содержимое модального окна...</span>';
        Modal::end();
        return ob_get_clean();
    }
    private function renderMediaModal()
    {
        ob_start();
        Modal::begin([
            'title' => '<span id="media-modal-title"</span>',
            'id' => 'mediaModal',
            'options' => ['class' => 'media-modal'],
            'dialogOptions' => ['class' => 'modal-dialog-centered modal-lg'],
        ]);
        echo '<div id="media-modal-content">Содержимое модального окна...</div>';
        Modal::end();
        return ob_get_clean();
    }


    private function renderCard($image)
    {
        $output = '<div class="card mb-3">';
        $output .= '<div class="card-body media-card-body">';

        if ($this->isVideo($image->src)) {
            $output .= '<video class="video-modal" data-src="' . $image->src . '" autoplay loop muted playsinline src="' . $image->src . '" alt="' . $image->alt . '"></video>';
        } else {
            $output .= '<img class="image-modal" data-src="' . $image->src . '" src="' . $image->src . '" alt="' . $image->alt . '">';
        }

        $output .= '<h5 class="card-title">' . Html::encode($image->alt) . '</h5>';
        $output .= '</div>';

        $output .= '<div class="card-footer">';
        $output .= $this->renderDropdown($image);
        $output .= '</div>';

        $output .= '</div>';
        return $output;
    }


    private function renderDropdown($image)
    {
        $output = '<ul class="nav nav-pills">';

        /*$output .= '<li class="nav-item">';
        $output .= '<a class="nav-link" href="' . $image->href . '">Просмотреть</a>';
        $output .= '</li>';*/

        $output .= '<li class="nav-item dropdown">';
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Отправить</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<a class="dropdown-item"   href="#">Одноклассники</a>';
        $output .= '<a class="dropdown-item" href="#">Вконтакте</a>';
        $output .= '<a class="dropdown-item" href="#">Мой мир</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item whatsapp" rel="nofollow noopener" target="_blank" href="https://api.whatsapp.com/send?text={{ url }}">
                                WhatsUp</a>';
        $output .= '<a class="dropdown-item telegram" rel="nofollow noopener" target="_blank" href="https://telegram.me/share/url?url={{ url }}">Телеграм</a>';
        $output .= '<a class="dropdown-item viber" rel="nofollow noopener" target="_blank" href="viber://forward?text={{ url }}">Вайбер</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item" href="#">Ссылка</a>';
        $output .= '<a class="dropdown-item" href="#">HTML</a>';
        $output .= '<a class="dropdown-item" href="#">BB-code</a>';
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

