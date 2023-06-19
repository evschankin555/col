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
    public $category;
    public $subCategory;
    public $files;
    public $is_prev;
    public $prev;
    private static $images;
/*
 {
  "@context": "http://schema.org",
  "@type": "VideoObject",
  "name": "Название видео",
  "description": "Описание видео",
  "thumbnailUrl": "URL изображения-миниатюры для видео",
  "uploadDate": "Дата загрузки видео в формате ISO 8601",
  "duration": "Продолжительность видео в формате ISO 8601",
  "contentUrl": "URL видео",
  "embedUrl": "URL для встраивания видео",
  "interactionStatistic": {
    "@type": "InteractionCounter",
    "interactionType": { "@type": "WatchAction" },
    "userInteractionCount": "Количество просмотров видео"
  }
}

 * */
    public static function getAll($page = 1, $categorySlug = null, $subCategorySlug = null)
    {
        if (self::$images === null) {
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;

            $cacheKey = "new_5_images_page{$page}_categorySlug{$categorySlug}_subCategorySlug{$subCategorySlug}";
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

            $jsonLdData = [];
            $img = '';
            foreach ($imagesData['data'] as $image) {
                $model = new static;
                $model->files = $image['files'];
                if ($image['is_prev'] == 1) {
                    $model->is_prev = true;
                    $model->prev = $image['prev'];
                }else{
                    $model->is_prev = false;
                }
                foreach ($model->files as $file) {
                    if($file['type'] == 'mp4') {
                        $model->src = $file['href'];
                        break;
                    }elseif( $file['type'] == 'webp'){
                        $model->src = $file['href'];
                    }
                }
                $model->alt = $image['alt'];
                $model->href = self::renderLink($image);
                $model->category = $image['category'];
                $model->subCategory = $image['subCategory'];
                self::$images[] = $model;

                // Добавляем данные JSON-LD для этого изображения/видео
                $jsonLdData[] = self::generateJsonLdData($model);

                if($img == ''){
                    $img = $image['src'];
                }
            }
        }

        return [
            'images' => self::$images,
            'pages' => $imagesData['pages'],
            'currentPage' => $page,
            'jsonLdData' => $jsonLdData,
            'img' => $img,
        ];
    }

    public static function getPagination($currentPage, $totalPages, $categorySlug = null, $subCategorySlug = null, $q = null) {
        if ($totalPages <= 1) {
            return [];
        }
        $pagination = [];

        // Создаем объект пагинации для кнопки "назад"
        $pagination[] = [
            'label' => '«',
            'url' => self::buildPageUrl($currentPage - 1, $categorySlug, $subCategorySlug, $q),
            'disabled' => $currentPage == 1,
            'active' => false
        ];

        // Создаем объект пагинации для первой страницы
        $pagination[] = [
            'label' => 1,
            'url' => self::buildPageUrl(1, $categorySlug, $subCategorySlug, $q),
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
                    'url' => self::buildPageUrl($i, $categorySlug, $subCategorySlug, $q),
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
            'url' => self::buildPageUrl($totalPages, $categorySlug, $subCategorySlug, $q),
            'disabled' => false,
            'active' => $currentPage == $totalPages
        ];

        // Создаем объект пагинации для кнопки "вперед"
        $pagination[] = [
            'label' => '»',
            'url' => self::buildPageUrl($currentPage + 1, $categorySlug, $subCategorySlug, $q),
            'disabled' => $currentPage == $totalPages,
            'active' => false
        ];

        return $pagination;
    }


    private static function buildPageUrl($page, $categorySlug = null, $subCategorySlug = null, $q = null) {
        // Если предоставлен поисковый запрос, строим URL для поиска
        if ($q !== null) {
            return "/search?q=" . urlencode($q) . "&page=$page";
        }

        // В противном случае строим URL для обычного просмотра
        $url = "";
        if ($categorySlug !== null) {
            $url .= "/{$categorySlug}";
        }
        if ($subCategorySlug !== null) {
            $url .= "/{$subCategorySlug}";
        }
        if ($page != 1) {
            $url .= "/page/{$page}";
        } else {
            $url .= "/";
        }
        return $url;
    }


    private static function renderLink($image)
    {
        $explode = explode('card/', $image['href']);
        $result = '/';
        if(isset($image['category'])){
            $result .=  $image['category']['slug'].'/';
        }
        if(isset($image['subCategory'])){
            $result .=  $image['subCategory']['slug'].'/';
        }
        $result .=self::translit($image['alt']) . '-card-' . $explode[1];
        return $result;
    }

    private static function translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',    'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'a',   'Б' => 'b',   'В' => 'v',
            'Г' => 'g',   'Д' => 'd',   'Е' => 'e',
            'Ё' => 'e',   'Ж' => 'zh',  'З' => 'z',
            'И' => 'i',   'Й' => 'y',   'К' => 'k',
            'Л' => 'l',   'М' => 'm',   'Н' => 'n',
            'О' => 'o',   'П' => 'p',   'Р' => 'r',
            'С' => 's',   'Т' => 't',   'У' => 'u',
            'Ф' => 'f',   'Х' => 'h',   'Ц' => 'c',
            'Ч' => 'ch',  'Ш' => 'sh',  'Щ' => 'sch',
            'Ь' => '',    'Ы' => 'y',   'Ъ' => '',
            'Э' => 'e',   'Ю' => 'yu',  'Я' => 'ya',
        );

        $string = strtr($string, $converter); // Транслитерация
        $string = strtolower($string); // Преобразование в нижний регистр
        $string = preg_replace('/\s+/', '-', $string); // Замена пробелов на тире
        $string = preg_replace('/[^a-zA-Z0-9\-]/', '', $string); // Удаление всего, что не буква или цифра или тире
        $string = preg_replace('/-+/', '-', $string); // Замена повторяющихся тире на один
        return $string;
    }

    public static function search($q, $page = 1)
    {
        $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

        $cache = Yii::$app->cache;
        $cacheKey = "search_images3_page{$page}_q{$q}";
        $imagesData = $cache->get($cacheKey);

        if ($imagesData === false) {
            $query = ['page' => $page, 'q' => $q];
            $response = $client->request('POST', 'images', ['query' => $query]);
            $imagesData = json_decode($response->getBody(), true);
            $cache->set($cacheKey, $imagesData, 300);
        }

        $jsonLdData = [];
        foreach ($imagesData['data'] as $image) {
            $model = new static;
            $model->src = $image['src'];
            $model->alt = $image['alt'];
            $model->href = self::renderLink($image);
            $model->category = $image['category'];
            $model->subCategory = $image['subCategory'];
            self::$images[] = $model;

            // Добавляем данные JSON-LD для этого изображения/видео
            $jsonLdData[] = self::generateJsonLdData($model);
        }

        return [
            'images' => self::$images,
            'pages' => $imagesData['pages'],
            'currentPage' => $page,
            'jsonLdData' => $jsonLdData,
        ];
    }

    private static function isVideo($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'mp4';
    }

    private static function generateJsonLdData($model) {
        $jsonData = [
            "@context" => "http://schema.org",
            "description" => $model->alt,
            "url" => $model->href
        ];

        if (self::isVideo($model->src)) {
            // Это видеофайл, добавляем соответствующие свойства
            $jsonData["@type"] = "VideoObject";
            $jsonData["contentUrl"] = $model->src;
            // Здесь можно добавить другие свойства VideoObject, если они доступны
        } else {
            // Это изображение, добавляем соответствующие свойства
            $jsonData["@type"] = "ImageObject";
            $jsonData["contentUrl"] = $model->src;
            // Здесь можно добавить другие свойства ImageObject, если они доступны
        }
        return $jsonData;
    }
}
