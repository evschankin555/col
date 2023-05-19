<?php
namespace app\assets;

use yii\web\AssetBundle;
class RegistrationFormWidgetAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [];
    public $jsOptions = [
        'position' => \yii\web\View::POS_READY,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];

    public function init()
    {
        $version = filemtime(\Yii::getAlias("@webroot/js/register-form.js"));
        $this->js = [
            'js/register-form.js?v=' . $version,
        ];

        parent::init();
    }
}
