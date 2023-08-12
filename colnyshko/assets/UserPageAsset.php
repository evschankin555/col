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
        $jsVersion = filemtime(\Yii::getAlias("@webroot/js/user-page.js"));
        $cssVersion = filemtime(\Yii::getAlias("@webroot/css/user-page.css"));

        $this->js = [
            'js/user-page.js?v=' . $jsVersion,
        ];

        $this->css = [
            'css/user-page.css?v=' . $cssVersion,
        ];

        parent::init();
    }
}
