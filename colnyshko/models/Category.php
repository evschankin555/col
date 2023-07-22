<?php
namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;
use app\models\SubCategory;
use app\components\ApiTimer;

class Category extends Model
{
    public $id;
    public $name;
    public $count;
    public $slug;
    public $alphabet;
    public $isActive;
    public $subCategories = [];


    private static $categories;

    public static function getAll($display = null)
    {
        if (self::$categories === null) {

            $query = [];
            if ($display !== null) {
                $query['display'] = $display;
            }

            // Идентификатор замера времени, состоящий из URL и параметров запроса.
            $timerId = 'https://legkie-otkrytki.ru/api/categories?' . http_build_query($query);

            // Начало замера времени.
            ApiTimer::start($timerId);
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;

            $cacheKey = "categories_new2_display{$display}";
            $categories = $cache->get($cacheKey);

            if ($categories === false) {
                $response = $client->request('GET', 'categories', ['query' => $query]);
                $categories = json_decode($response->getBody(), true);
                $cache->set($cacheKey, $categories, 300);
            }

            // Завершение замера времени.
            ApiTimer::end($timerId, $timerId);
            // Проверяем наличие ключа "data" в $categories
            if (!isset($categories['data']) || !is_array($categories['data'])) {
                return []; // или какой-либо другой дефолтный ответ
            }

            // Создаем модель для категории "Все" и добавляем ее в начало списка
            $allCategoryModel = new static;
            $allCategoryModel->id = 0;
            $allCategoryModel->name = 'Все';
            $allCategoryModel->isActive = false; // по умолчанию категория "Все" активна

            $allCount = 0;  // Вводим переменную для подсчета общего количества картинок

            if (isset($categories['data']) && is_array($categories['data'])) {
                foreach ($categories['data'] as $category) {
                    $model = new static;
                    $model->id = $category['id'];
                    $model->name = $category['name'];
                    $model->slug = $category['slug'];
                    $model->alphabet = ($category['alphabet'] == 1) ? true : false;
                    $model->isActive = false; // по умолчанию все категории неактивны

                    $count = 0;
                    // Создаем модели подкатегорий и добавляем их в свойство модели категории
                    foreach ($category['subCategories'] as $subCategory) {
                        $subCategoryModel = new SubCategory();
                        $subCategoryModel->id = $subCategory['id'];
                        $subCategoryModel->name = $subCategory['name'];
                        $subCategoryModel->slug = $subCategory['slug'];
                        $subCategoryModel->month = $subCategory['month'];
                        $count += $subCategoryModel->count = self::formatCount($subCategory['count']);
                        $model->subCategories[] = $subCategoryModel;
                    }

                    $model->count = self::formatCount($count);
                    self::$categories[] = $model;

                    // Прибавляем количество картинок в категории к общему количеству
                    $allCount += $count;
                }
            }

            // Присваиваем общее количество картинок модели "Все"
            $allCategoryModel->count = self::formatCount($allCount);

            // Добавляем модель "Все" в начало списка категорий
            array_unshift(self::$categories, $allCategoryModel);
        }

        return self::$categories;
    }

    public static function setActive($slug)
    {
        if (is_array(self::$categories) || is_object(self::$categories)) {
            foreach (self::$categories as $category) {
                if ($category->slug == $slug) {
                    $category->isActive = true;
                    break;
                }
            }
        }
    }


    public static function setActiveSubCategory($slug)
    {
        if (is_array(self::$categories) || is_object(self::$categories)) {
            foreach (self::$categories as $category) {
                if (is_array($category->subCategories) || is_object($category->subCategories)) {
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
    }


    public static function formatCount($count)
    {
        if ($count > 999) {
            return number_format($count, 0, '', ' ');
        } else {
            return $count;
        }
    }

}
