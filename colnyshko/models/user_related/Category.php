<?php
namespace app\models\user_related;

use Yii;
use app\components\TimedActiveRecord;
use app\models\User;
class Category extends TimedActiveRecord
{
    public static function tableName()
    {
        return 'categories';
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

    // Добавлен метод для получения связанных изображений с категорией
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])->viaTable('image_relations', ['category_id' => 'id']);
    }
    public static function getCategoriesForCollection($userId, $collectionId)
    {
        return self::find()
            ->distinct()
            ->innerJoin('image_relations', 'categories.id = image_relations.category_id')
            ->where(['image_relations.user_id' => $userId, 'image_relations.collection_id' => $collectionId])
            ->andWhere(['<>', 'image_relations.category_id', 0])
            ->all();
    }

}
