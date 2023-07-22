<?php
namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use app\components\DbTimer;
use app\components\TimedActiveQuery;

class TimedActiveRecord extends ActiveRecord {

    public static function find() {
        return Yii::createObject(TimedActiveQuery::className(), [get_called_class()]);
    }

}