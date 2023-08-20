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
            [['collection_id', 'category_id', 'image_id'], 'default', 'value' => null],
            [['title'], 'string', 'min' => 10, 'max' => 100],
            [['description'], 'string', 'min' => 20, 'max' => 1000],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection_id' => 'Collection ID',
            'category_id' => 'Category ID',
            'image_id' => 'Image ID',
            'title' => 'Title',
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

    public static function createNew($image_id, $collection_id, $category_id, $title, $description)
    {
        $relation = new ImageRelation();
        $relation->image_id = $image_id;
        $relation->collection_id = $collection_id !== 0 ? $collection_id : null;
        $relation->category_id = $category_id !== 0 ? $category_id : null;
        $relation->title = $title;
        $relation->description = $description;

        if ($relation->save()) {
            return $relation;
        } else {
            return null;
        }
    }

}
