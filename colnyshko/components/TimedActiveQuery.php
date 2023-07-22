<?php
namespace app\components;

use Yii;
use yii\db\ActiveQuery;
use app\components\DbTimer;

class TimedActiveQuery extends ActiveQuery {

    public function all($db = null) {
        $timerId = $this->modelClass;

        DbTimer::start($timerId);

        $result = parent::all($db);

        DbTimer::end($timerId);

        return $result;
    }

    public function one($db = null) {
        $timerId = $this->modelClass;

        DbTimer::start($timerId);

        $result = parent::one($db);

        DbTimer::end($timerId);

        return $result;
    }

    public function count($q = '*', $db = null) {
        $timerId = $this->modelClass;

        DbTimer::start($timerId);

        $result = parent::count($q, $db);

        DbTimer::end($timerId);

        return $result;
    }

    public function exists($db = null) {
        $timerId = $this->modelClass;

        DbTimer::start($timerId);

        $result = parent::exists($db);

        DbTimer::end($timerId);

        return $result;
    }

    // Здесь вы можете добавить другие методы, если нужно
}
