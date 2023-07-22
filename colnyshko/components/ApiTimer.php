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
}
