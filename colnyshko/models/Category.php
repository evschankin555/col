<?php
namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;

class Category extends Model
{
    public $id;
    public $name;
    public $isActive;

    private static $categories;

    public static function getAll()
    {
        if (self::$categories === null) {
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;

            $categories = $cache->get('categories');

            if ($categories === false) {
                $response = $client->request('GET', 'categories');
                $categories = json_decode($response->getBody(), true);
                $cache->set('categories', $categories, 300);
            }

            // Создаем модель для категории "Все" и добавляем ее в начало списка
            $allCategoryModel = new static;
            $allCategoryModel->id = 0;
            $allCategoryModel->name = 'Все';
            $allCategoryModel->isActive = false; // по умолчанию категория "Все" активна
            self::$categories[] = $allCategoryModel;

            foreach ($categories['data'] as $category) {
                $model = new static;
                $model->id = $category['id'];
                $model->name = $category['name'];
                $model->isActive = false; // по умолчанию все категории неактивны
                self::$categories[] = $model;
            }
        }

        return self::$categories;
    }


    public static function setActive($id)
    {
        foreach (self::$categories as $category) {
            if ($category->id == $id) {
                $category->isActive = true;
                break;
            }
        }
    }
}
