<?php
namespace app\assets;

use yii\web\AssetBundle;

class RegistrationFormWidgetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
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
        $version = filemtime(\Yii::getAlias("@webroot/js/AuthWidget/register-form.js"));
        $this->js = [
            'js/AuthWidget/register-form.js?v=' . $version,
        ];

        parent::init();
    }
}
