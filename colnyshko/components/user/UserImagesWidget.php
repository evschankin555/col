<?php
namespace app\components\user;

use yii\base\Widget;
use yii\helpers\Html;

class UserImagesWidget extends Widget
{
    public $images;

    public function run()
    {
        $output = '<div class="grid">';
        foreach ($this->images as $image) {
            $output .= '<div class="grid-item">';
            $output .= $this->renderCard($image);
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }

    private function renderCard($imageRelation)
    {
        $image = $imageRelation->image;

        $output = '<div class="card mb-2">';
        $output .= '<div class="card-body media-card-body">';

        $url = $image->url;
        $src = $url;

        $output .= '
        <img class="user-image-modal" src="' . $src . '" alt="' . Html::encode($imageRelation->description) . '">';

        $output .= '<h5 class="card-title">' . Html::encode($imageRelation->title) . '</h5>';

        $output .= '</div>';
        $output .= '<div class="card-footer">';

        $output .= $this->renderDropdown($image);
        $output .= $this->renderDownloadMenu($image);
        $output .= '</div>';
        $output .= '</div>';

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
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">&nbsp;</a>';
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

    private function renderDownloadMenu($image)
    {
    /*    $output = '<ul class="nav nav-pills">';
        $output .= '<li class="nav-item dropdown images-menu-download">';
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Скачать</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';

        if (is_array($image->files) || is_object($image->files)) {
            foreach ($image->files as $file) {
                $output .= '<a class="dropdown-item ' . $file['type'] . '" href="' . $file['href'] . '" download target="_blanck">&nbsp;</a>';
            }
        }

        $output .= '</div>';
        $output .= '</li>';
        $output .= '</ul>';

        return $output;*/
    }
}

