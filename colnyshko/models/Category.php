<?php
namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;
use app\models\SubCategory;

class Category extends Model
{
    public $id;
    public $name;
    public $count;
    public $slug;
    public $isActive;
    public $subCategories = [];


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
                $model->slug = $category['slug'];
                $model->isActive = false; // по умолчанию все категории неактивны

                $count = 0;
                // Создаем модели подкатегорий и добавляем их в свойство модели категории
                foreach ($category['subCategories'] as $subCategory) {
                    $subCategoryModel = new SubCategory();
                    $subCategoryModel->id = $subCategory['id'];
                    $subCategoryModel->name = $subCategory['name'];
                    $subCategoryModel->slug = $subCategory['slug'];
                    $count += $subCategoryModel->count = $subCategory['count'];
                    $model->subCategories[] = $subCategoryModel;
                }

                $model->count = $count;
                self::$categories[] = $model;
            }
        }

        return self::$categories;
    }

    public static function setActive($slug)
    {
        foreach (self::$categories as $category) {
            if ($category->slug == $slug) {
                $category->isActive = true;
                break;
            }
        }
    }

    public static function setActiveSubCategory($slug)
    {
        foreach (self::$categories as $category) {
            foreach ($category->subCategories as $subCategory) {
                if ($subCategory->slug == $slug) {
                    $subCategory->isActive = true;
                    $category->isActive = true;  // активируем родительскую категорию
                    break 2;  // прерываем оба цикла
                }
            }
        }
    }


}
