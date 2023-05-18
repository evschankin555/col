<?php
namespace app\assets;

use yii\web\AssetBundle;
class LoginFormWidgetAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [];
    public $jsOptions = [
        'position' => \yii\web\View::PH_BODY_END,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public function init()
    {
        $version = filemtime(\Yii::getAlias("@webroot/js/login-form.js"));
        $this->js = [
            'js/login-form.js?v=' . $version,
        ];

        parent::init();
    }
}

