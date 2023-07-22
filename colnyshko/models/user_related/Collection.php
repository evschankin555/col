<?php
namespace app\models\user_related;

use Yii;
use app\components\TimedActiveRecord;

class Collection extends TimedActiveRecord
{
    public static function tableName()
    {
        return 'collections';
    }

    public function rules()
    {
        return [
            [['user_id', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getImages()
    {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])->viaTable('image_relations', ['collection_id' => 'id']);
    }


}
