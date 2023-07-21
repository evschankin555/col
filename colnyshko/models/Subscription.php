<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Subscription extends ActiveRecord
{
    public static function tableName()
    {
        return 'subscriptions';
    }

    public function rules()
    {
        return [
            [['user_id', 'subscriber_id'], 'required'],
            [['user_id', 'subscriber_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'subscriber_id' => 'Subscriber ID'
        ];
    }
}
