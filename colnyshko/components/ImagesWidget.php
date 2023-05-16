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
        $output .= '<div class="card-body">';

        if ($this->isVideo($image->src)) {
            $output .= '<video autoplay loop muted playsinline src="' . $image->src . '"></video>';
        } else {
            $output .= '<img src="' . $image->src . '" alt="' . $image->alt . '">';
        }

        $output .= '<h5 class="card-title">' . Html::encode($image->alt) . '</h5>';
        $output .= '</div>';

        $output .= '<div class="card-footer">';
        $output .= '<a href="' . $image->href . '" class="card-link">Просмотреть</a>';
        $output .= '</div>';

        $output .= '</div>';
        return $output;
    }

    private function isVideo($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'mp4';
    }
}
