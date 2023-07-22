<?php


namespace app\models;

use Yii;
use app\components\TimedActiveRecord;


class Tooltip extends TimedActiveRecord

{
    public static function tableName()
    {
        return 'tooltip';
    }

    public function rules()
    {
        return [
            [['id', 'message', 'language'], 'required'],
            [['id', 'language'], 'string', 'max' => 50],
            [['message'], 'string']
        ];
    }

    public static function getTooltip($id, $language = 'en')
    {
        return static::find()->where(['id' => $id, 'language' => $language])->one();
    }
}
