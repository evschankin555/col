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
            [['image_id', 'user_id'], 'required'],
            [['collection_id', 'category_id', 'image_id', 'user_id'], 'default', 'value' => null],
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
            'user_id' => 'User ID',
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

    public static function createNew($image_id, $collection_id, $category_id, $title, $description, $user_id)
    {
        $relation = new ImageRelation();
        $relation->image_id = $image_id;
        $relation->collection_id = $collection_id !== 0 ? $collection_id : null;
        $relation->category_id = $category_id !== 0 ? $category_id : null;
        $relation->title = $title;
        $relation->description = $description;
        $relation->user_id = $user_id;

        if ($relation->save()) {
            return $relation;
        } else {
            return null;
        }
    }

    public static function getImagesByCriteria($userId, $collectionId = null, $categoryId = null)
    {
        $condition = ['user_id' => $userId];

        if ($collectionId !== null && $collectionId != 0) {
            $condition['collection_id'] = $collectionId;
        }

        if ($categoryId !== null && $categoryId != 0) {
            $condition['category_id'] = $categoryId;
        }

        return self::find()
            ->where($condition)
            ->with('image')
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    public static function getCollectionsForCategory($userId, $categoryId)
    {
        return self::find()
            ->select('collection_id')
            ->distinct()
            ->where(['user_id' => $userId, 'category_id' => $categoryId])
            ->andWhere(['<>', 'collection_id', 0]) // Исключаем записи, где collection_id равен 0
            ->joinWith('collection')  // Присоединяем таблицу коллекций для дополнительной информации
            ->all();
    }


    public static function getCategoriesForCollection($userId, $collectionId)
    {
        return self::find()
            ->select('category_id')
            ->distinct()
            ->where(['image_relations.user_id' => $userId, 'collection_id' => $collectionId])
            ->andWhere(['<>', 'category_id', 0]) // Исключаем записи, где category_id равен 0
            ->joinWith('category')  // Присоединяем таблицу категорий для дополнительной информации
            ->all();
    }


}
