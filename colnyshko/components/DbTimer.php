<?php
namespace app\components;

use Yii;

class DbTimer {

    public static function start($id) {
        Yii::$app->params['DB_EXECUTION_TIMES'][$id] = [
            'model' => $id,
            'startTime' => microtime(true)
        ];
    }

    public static function end($id) {
        if (isset(Yii::$app->params['DB_EXECUTION_TIMES'][$id])) {
            Yii::$app->params['DB_EXECUTION_TIMES'][$id]['executionTime'] = microtime(true) - Yii::$app->params['DB_EXECUTION_TIMES'][$id]['startTime'];
        }
    }

    public static function getExecutionTimes() {
        $executionTimes = isset(Yii::$app->params['DB_EXECUTION_TIMES']) ? Yii::$app->params['DB_EXECUTION_TIMES'] : [];

        if (empty($executionTimes)) {
            return '';
        }

        $result = '<br /><br /><b>Время выполнения запросов к БД:</b><br />';
        foreach ($executionTimes as $id => $data) {
            $formattedModelName = str_replace('app\models\\', '', $data['model']);
            $result .= $formattedModelName . ' - ' . number_format($data['executionTime'], 6) . ' сек.<br />';
        }
        $result = '<small>' . $result . '</small>';
        return $result;
    }

}
