<?php
namespace app\models\user_related;

use Yii;
use app\components\TimedActiveRecord;

class Image extends TimedActiveRecord
{
    public static function tableName()
    {
        return 'images';
    }

    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url'], 'string', 'max' => 2048],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Image URL',
            'description' => 'Description',
        ];
    }
}
