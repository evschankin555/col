<?php
namespace app\components;
use Yii;
class ApiTimer {

    private static $startTimes = [];

    public static function start($id) {
        Yii::$app->params['API_EXECUTION_TIMES'][$id] = [
            'apiUrl' => $id,
            'startTime' => microtime(true)
        ];
    }

    public static function end($id, $apiUrl) {
        if (isset(Yii::$app->params['API_EXECUTION_TIMES'][$id])) {
            Yii::$app->params['API_EXECUTION_TIMES'][$id]['executionTime'] = microtime(true) - Yii::$app->params['API_EXECUTION_TIMES'][$id]['startTime'];
            Yii::$app->params['API_EXECUTION_TIMES'][$id]['apiUrl'] = $apiUrl;
        }
    }

    public static function getExecutionTimes() {
        $executionTimes = isset(Yii::$app->params['API_EXECUTION_TIMES']) ? Yii::$app->params['API_EXECUTION_TIMES'] : [];

        // Если массив запросов пуст, возвращаем пустую строку.
        if (empty($executionTimes)) {
            return '';
        }

        $result = '<br />Время выполнения запросов к API:<br />';
        foreach ($executionTimes as $id => $data) {
            $result .= $data['apiUrl'] . ' - ' . number_format($data['executionTime'], 6) . ' сек.<br />';
        }

        return $result;
    }
    public static function getTotalExecutionTime() {
        $endTime = microtime(true);
        $executionTime = $endTime - $_SERVER["REQUEST_TIME_FLOAT"];
        return 'Время до рендеринга: ' . number_format($executionTime, 6) . ' сек.<br />';
    }
    public static function getRenderingTime() {
        if (!isset(Yii::$app->params['startTime'])) {
            // Можете вернуть какое-то умолчательное значение или пустую строку
            return 'Время рендеринга не определено<br />';
        }

        $endTime = microtime(true);
        $executionTime = $endTime - Yii::$app->params['startTime'];
        return 'Время рендеринга: ' . number_format($executionTime, 6) . ' сек.<br />';
    }

    public static function getSystemInfo() {
        // Версия PHP
        $phpVersion = phpversion();
        return 'PHP Version: ' . $phpVersion;

    }


}
