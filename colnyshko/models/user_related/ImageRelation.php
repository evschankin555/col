<?php
namespace app\models\user_related;

use Yii;
use app\components\TimedActiveRecord;
use app\models\User;

class ImageRelation extends TimedActiveRecord
{
    public static function tableName()
    {
        return 'image_relations';
    }

    public function rules()
    {
        return [
            [['image_id', 'user_id', 'username'], 'required'],
            [['collection_id', 'category_id', 'image_id', 'user_id'], 'default', 'value' => null],
            [['title'], 'string', 'min' => 10, 'max' => 100],
            [['description'], 'string', 'min' => 20, 'max' => 1000],
            [['is_deleted'], 'boolean'],
            [['username'], 'string', 'max' => 50],
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
            'is_deleted' => 'Is Deleted',
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
        $username = \app\models\User::find()->where(['id' => $user_id])->one()->username;

        $relation = new ImageRelation();
        $relation->image_id = $image_id;
        $relation->collection_id = $collection_id !== 0 ? $collection_id : null;
        $relation->category_id = $category_id !== 0 ? $category_id : null;
        $relation->title = $title;
        $relation->description = $description;
        $relation->user_id = $user_id;
        $relation->username = $username;

        if ($relation->save()) {
            return $relation;
        } else {
            return null;
        }
    }

    public static function getImagesByCriteria($userId, $collectionId = null, $categoryId = null)
    {
        $condition = ['user_id' => $userId, 'is_deleted' => 0];

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
            ->where(['user_id' => $userId, 'category_id' => $categoryId, 'is_deleted' => 0])
            ->andWhere(['<>', 'collection_id', 0])
            ->joinWith('collection')
            ->all();
    }

    public static function getCategoriesForCollection($userId, $collectionId)
    {
        return self::find()
            ->select('category_id')
            ->distinct()
            ->where(['image_relations.user_id' => $userId, 'collection_id' => $collectionId, 'is_deleted' => 0])
            ->andWhere(['<>', 'category_id', 0])
            ->joinWith('category')
            ->all();
    }

    public static function getImageURLById($id)
    {
        $imageRelation = self::find()
            ->where(['id' => $id])
            ->with('image')
            ->one();

        if ($imageRelation && $imageRelation->image) {
            return $imageRelation->image->url;
        }

        return null;
    }

    public function updateCollectionAndCategory($newCollectionId, $newCategoryId) {
        $changed = false;

        if ($this->collection_id !== $newCollectionId) {
            $this->collection_id = $newCollectionId !== 0 ? $newCollectionId : null;
            $changed = true;
        }

        if ($this->category_id !== $newCategoryId) {
            $this->category_id = $newCategoryId !== 0 ? $newCategoryId : null;
            $changed = true;
        }

        if ($changed) {
            return $this->save();
        }

        return false;
    }

    public function markAsDeleted()
    {
        $this->is_deleted = 1;
        return $this->save();
    }

    public static function getImagesNewHome()
    {
        $condition = ['is_deleted' => 0];

        return self::find()
            ->where($condition)
            ->with('image')
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    public static function getAllImages()
    {
        return self::find()
            ->select('*')
            ->where(['is_deleted' => 0])
            ->with('image')
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }


}
