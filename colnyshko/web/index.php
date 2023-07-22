<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
$_SERVER["REQUEST_TIME_FLOAT"] = microtime(true);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
$app = new yii\web\Application($config);

$app->on(yii\web\Application::EVENT_AFTER_REQUEST, function ($event) {
    /* @var $event \yii\base\Event */
    /* @var $app yii\web\Application */
    $app = $event->sender;
    $app->params['timingOutput'] = '<br /><br /><small>'
        . app\components\ApiTimer::getTotalExecutionTime()
        . app\components\ApiTimer::getRenderingTime()
        . app\components\ApiTimer::getExecutionTimes()
        . app\components\DbTimer::getExecutionTimes()
        . '</small>';
});


$app->response->on(yii\web\Response::EVENT_AFTER_SEND, function ($event) {
    /* @var $event \yii\base\Event */
    /* @var $response yii\web\Response */
    $response = $event->sender;
    echo Yii::$app->params['timingOutput'];
});

$app->run();
