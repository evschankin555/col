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
        <a href="' . $url . '">
        <img class="user-image-modal" src="' . $src . '" alt="' . Html::encode($imageRelation->description) . '">';
        $output .= '</a>';

        $output .= '<h5 class="card-title">' . Html::encode($imageRelation->title) . '</h5>';

        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

