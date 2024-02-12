<?php

namespace app\components;

use Yii;
use yii\base\Component;

class OpcacheManager extends Component
{
    public function reset()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
            Yii::info('OPCache has been reset.');
            return true;
        } else {
            Yii::warning('OPCache reset function is not available.');
            return false;
        }
    }
}
