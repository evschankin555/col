<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

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

        return $output;
    }

    private function renderCard($image)
    {
        $output = '<div class="card mb-3">';
        $output .= '<div class="card-header">';
        $output .= $this->renderDropdown($image);
        $output .= '</div>';
        $output .= '<div class="card-body">';

        if ($this->isVideo($image->src)) {
            $output .= '<video autoplay loop muted playsinline src="' . $image->src . '"></video>';
        } else {
            $output .= '<img src="' . $image->src . '" alt="' . $image->alt . '">';
        }

        $output .= '<h5 class="card-title">' . Html::encode($image->alt) . '</h5>';
        $output .= '</div>';

        $output .= '<div class="card-footer">';
        $output .= '<a class="card-link" href="' . $image->href . '">Просмотреть</a>';
        $output .= '</div>';

        $output .= '</div>';
        return $output;
    }

    private function renderDropdown($image)
    {
        $output = '<ul class="nav nav-pills float-end">';

        $output .= '<li class="nav-item dropdown">';
        $output .= '<a class="nav-link dropdown-toggle show" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Отправить</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<a class="dropdown-item" href="#">Одноклассники</a>';
        $output .= '<a class="dropdown-item" href="#">Вконтакте</a>';
        $output .= '<a class="dropdown-item" href="#">Мой мир</a>';
        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item" href="#">Вотсап</a>';
        $output .= '<a class="dropdown-item" href="#">Телеграм</a>';
        $output .= '<a class="dropdown-item" href="#">Вайбер</a>';
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

