<?php


namespace app\components;

use yii\base\Component;
use app\models\Collection;
use app\models\Category;

class CardPageComponent extends Component
{
    public function getFormattedSubscribersCount($model)
    {
        return $model->getFormattedSubscribersCount();
    }

    public function getCollectionInfo($collection, $imageRelation)
    {
        if ($collection) {
            return [
                'name' => $collection->name,
                'url' => '/' . $imageRelation->username . '/collection/' . $collection->id,
            ];
        }
        return null;
    }

    public function getCategoryInfo($category, $imageRelation)
    {
        if ($category) {
            return [
                'name' => $category->name,
                'url' => '/' . $imageRelation->username . '/category/' . $category->id,
            ];
        }
        return null;
    }
}
