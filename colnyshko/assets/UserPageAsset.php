<?php
namespace app\assets;

use yii\web\AssetBundle;
class UserPageAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public function init()
    {
        $jsVersionCardManager = filemtime(\Yii::getAlias("@webroot/js/card-manager.js"));
        $jsVersion = filemtime(\Yii::getAlias("@webroot/js/user-page.js"));
        $cssVersion = filemtime(\Yii::getAlias("@webroot/css/user-page.css"));

        $this->js = [
            'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js',
            'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js',
            'js/card-manager.js?v=' . $jsVersionCardManager,
            'js/user-page.js?v=' . $jsVersion,
        ];

        $this->css = [
            'css/user-page.css?v=' . $cssVersion,
        ];

        parent::init();
    }
}
