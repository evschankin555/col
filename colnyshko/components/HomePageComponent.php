<?php
namespace app\components;

use yii\base\Component;
use yii\helpers\Html;
use app\components\user\UserImagesWidget;

class HomePageComponent extends Component
{
    public $images;

    public function renderImagesList()
    {
        $listImages = UserImagesWidget::widget([
            'images' => $this->images
        ]);

        return $listImages;
    }
    public function renderLeftCard()
    {
        $output = '<div class="card border-secondary mb-3">
            <div class="card-header card-title text-center">
                
            </div>
            <div class="card-body text-center">
                
                <div class="user-username">
                    <h3 id="user-login">@jlk</h3>
                </div>
                <div class="form-group buttons">';



        $output .= '</div>
            </div>
            <div class="card-footer statistics">
                <small>Подписчиков: <span class="subscribersCount">890</span></small>
                <small>Подписок: <span class="subscriptionsCount">8989</span></small>
            </div>
        </div>';
        return $output;
    }
}
