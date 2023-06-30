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

        $output .= $this->renderBadges($image);
        if ($this->isVideo($image->src)) {
            $output .= '<video class="video-modal" 
            data-alt="' . Html::encode($image->alt) . '" 
            data-src="' . $image->src . '" autoplay loop muted playsinline src="' . $image->src . '" alt="' . $image->alt . '" data-href="'.$image->href.'"></video>';
        } else {
            $output .= '<img class="image-modal" data-html="HTML код..." data-bb="BB код..."  data-src="' . $image->src . '" src="' . $image->src . '" alt="' . $image->alt . '" data-href="'.$image->href.'">';
        }

        $output .= '<h5 class="card-title">' . Html::encode($image->alt) . '</h5>';
        $output .= '<div id="ok_shareWidget_'.$id.'" class="ok_class"></div>';
        $output .= '</div>';

        $output .= '<div class="card-footer">';
        $output .= $this->renderDropdown($image);
        $output .= $this->renderDownloadMenu($image);
        $output .= $this->renderInfoMenu($image);
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
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="true" aria-expanded="false">Отправить</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        $output .= '<a class="dropdown-item ok" rel="nofollow noopener" target="_blank" href="https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl='.$url.'">Одноклассники</a>';
        $output .= '<a class="dropdown-item vk" rel="nofollow noopener" target="_blank" href="https://vk.com/share.php?url='.$url.'">Вконтакте</a>';
        $output .= '<a class="dropdown-item mm" rel="nofollow noopener" target="_blank"  href="https://connect.mail.ru/share?url='.$url.'">Мой мир</a>';

        $output .= '<div class="dropdown-divider"></div>';
        $output .= '<a class="dropdown-item whatsapp" rel="nofollow noopener" target="_blank" href="https://api.whatsapp.com/send?text='.$url.'">WhatsUp</a>';
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
    private function isVideo($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'mp4';
    }
    private function renderDownloadMenu($image)
    {
        $output = '<ul class="nav nav-pills">';
        $output .= '<li class="nav-item dropdown images-menu-download">';
        $output .= '<a class="nav-link show" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="true" aria-expanded="false">Скачать</a>';
        $output .= '<div class="dropdown-menu" data-popper-placement="bottom-start">';
        foreach ($image->files as $file) {
            $output .= '<a class="dropdown-item ' . $file['type'] . '" href="' . $file['href'] . '" download target="_blanck">&nbsp;</a>';
        }
        $output .= '</div>';
        $output .= '</li>';
        $output .= '</ul>';

        return $output;
    }

    private function renderInfoMenu($image)
    {
        $output = '<ul class="nav nav-pills">';
        $output .= '<li class="nav-item dropdown images-menu-info">';
        $output .= '<button class="nav-link show" href="" role="button" data-bs-container="body" data-bs-toggle="popover" data-bs-html="true" data-bs-placement="top" data-bs-content="<b>Автор:</b> неизвестен<br><b>Источник:</b> <a href=\'https://otkritkis.com\' target=\'_blank\' rel=\'nofollow noopener\' class=\'external-link-icon\'>otkritkis.com</a>" data-bs-original-title="Информация о открытке" aria-describedby="popover18801">Инфо</button>';
        $output .= '</li>';
        $output .= '</ul>';
        return $output;
    }



    private function renderBadges($image) {
        $class = '';
        if(count($image->files) < 4){
            $class = ' short';
        }
        $output = '<div class="badges-container-image'.$class.'">';

        foreach ($image->files as $file) {
            $badgeColor = $this->getBadgeColor($file['type']);
            $fileType = ucfirst($file['type']);
            $output .= '<span class="badge bg-'.$badgeColor.'">' . $fileType . '</span>';

        }

        $output .= '</div>';

        return $output;
    }
    private function getBadgeColor($fileType) {
        switch($fileType) {
            case 'mp4':
                return 'primary';
            case 'gif':
                return 'success';
            case 'webp':
                return 'info';
            case 'jpg':
                return 'danger';
            default:
                return 'secondary';
        }
    }

}
