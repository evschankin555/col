<?php
namespace app\assets;

use yii\web\AssetBundle;

class PasswordRecoveryFormWidgetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public function init()
    {
        $version = filemtime(\Yii::getAlias("@webroot/js/AuthWidget/restore-form.js"));
        $this->js = [
            'js/AuthWidget/restore-form.js?v=' . $version,
        ];

        parent::init();
    }
}
