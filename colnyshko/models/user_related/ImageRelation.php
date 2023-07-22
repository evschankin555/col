<?php
namespace app\models\user_related;

use Yii;
use app\components\TimedActiveRecord;

class ImageRelation extends TimedActiveRecord
{
    public static function tableName()
    {
        return 'image_relations';
    }

    public function rules()
    {
        return [
            [['image_id'], 'required'],
            [['collection_id', 'category_id', 'image_id'], 'integer'],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection_id' => 'Collection ID',
            'category_id' => 'Category ID',
            'image_id' => 'Image ID',
            'description' => 'Description',
        ];
    }

    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['id' => 'collection_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getImage()
    {
        return $this->hasOne(Image::className(), ['id' => 'image_id']);
    }
}
