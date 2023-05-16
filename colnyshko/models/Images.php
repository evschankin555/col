<?php

namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;

class Images extends Model
{
    public $src;
    public $alt;
    public $href;

    private static $images;

    public static function getAll($page = 1, $categorySlug = null, $subCategorySlug = null)
    {
        if (self::$images === null) {
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;

            $cacheKey = "new5_images_page{$page}_categorySlug{$categorySlug}_subCategorySlug{$subCategorySlug}";
            $imagesData = $cache->get($cacheKey);

            if ($imagesData === false) {
                $query = ['page' => $page];
                if ($categorySlug !== null) {
                    $query['category_slug'] = $categorySlug;
                }
                if ($subCategorySlug !== null) {
                    $query['subCategory_slug'] = $subCategorySlug;
                }
                $response = $client->request('GET', 'images', ['query' => $query]);
                $imagesData = json_decode($response->getBody(), true);
                $cache->set($cacheKey, $imagesData, 300);
            }

            foreach ($imagesData['data'] as $image) {
                $model = new static;
                $model->src = $image['src'];
                $model->alt = $image['alt'];
                $model->href = $image['href'];
                self::$images[] = $model;
            }
        }

        return [
            'images' => self::$images,
            'pages' => $imagesData['pages'],
            'currentPage' => $page,
        ];
    }

    public static function getPagination($currentPage, $totalPages, $categorySlug = null, $subCategorySlug = null) {
        $pagination = [];

        // Создаем объект пагинации для кнопки "назад"
        $pagination[] = [
            'label' => '«',
            'url' => self::buildPageUrl($currentPage - 1, $categorySlug, $subCategorySlug),
            'disabled' => $currentPage == 1,
            'active' => false
        ];

        // Создаем объект пагинации для первой страницы
        $pagination[] = [
            'label' => 1,
            'url' => self::buildPageUrl(1, $categorySlug, $subCategorySlug),
            'disabled' => false,
            'active' => $currentPage == 1
        ];

        if ($currentPage > 4) {
            // Добавляем "пропуск", если текущая страница больше 4
            $pagination[] = [
                'label' => '...',
                'url' => null,
                'disabled' => true,
                'active' => false
            ];
        }

        // Создаем объекты пагинации для каждой страницы
        for ($i = 2; $i < $totalPages; $i++) {
            if ($i >= ($currentPage - 2) && $i <= ($currentPage + 2)) {
                $pagination[] = [
                    'label' => $i,
                    'url' => self::buildPageUrl($i, $categorySlug, $subCategorySlug),
                    'disabled' => false,
                    'active' => $i == $currentPage
                ];
            }
        }

        if ($currentPage < ($totalPages - 3)) {
            // Добавляем "пропуск", если текущая страница меньше (totalPages - 3)
            $pagination[] = [
                'label' => '...',
                'url' => null,
                'disabled' => true,
                'active' => false
            ];
        }

        // Создаем объект пагинации для последней страницы
        $pagination[] = [
            'label' => $totalPages,
            'url' => self::buildPageUrl($totalPages, $categorySlug, $subCategorySlug),
            'disabled' => false,
            'active' => $currentPage == $totalPages
        ];

        // Создаем объект пагинации для кнопки "вперед"
        $pagination[] = [
            'label' => '»',
            'url' => self::buildPageUrl($currentPage + 1, $categorySlug, $subCategorySlug),
            'disabled' => $currentPage == $totalPages,
            'active' => false
        ];

        return $pagination;
    }


    private static function buildPageUrl($page, $categorySlug = null, $subCategorySlug = null) {
        $url = "";
        if ($categorySlug !== null) {
            $url .= "/{$categorySlug}";
        }
        if ($subCategorySlug !== null) {
            $url .= "/{$subCategorySlug}";
        }
        if ($page != 1) {
            $url .= "/page/{$page}";
        }else{
            $url .= "/";
        }
        return $url;
    }


}
