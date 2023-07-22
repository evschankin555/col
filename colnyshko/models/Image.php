<?php
namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;

use app\components\ApiTimer;
class Image extends Model
{
    public $src;
    public $alt;
    public $width;
    public $height;
    public $href;
    public $category;
    public $subCategory;
    public $jsonLdData;
    public $files;

    private static $image;

    public static function get($hash)
    {
        if (self::$image === null) {
            // Запрос
            $query = ['hash' => $hash];


            // Идентификатор замера времени, состоящий из URL и параметров запроса.
            $timerId = 'https://legkie-otkrytki.ru/api/image?' . http_build_query($query);

            // Начало замера времени.
            ApiTimer::start($timerId);
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;
            $cacheKey = "image_new_{$hash}_7";
            $imageData = $cache->get($cacheKey);

            if ($imageData === false) {
                $response = $client->request('GET', 'image', ['query' => $query]);


                $imageData = json_decode($response->getBody(), true);
                if($imageData['result'] === 'success') {
                    $cache->set($cacheKey, $imageData['image'], 300);
                    $imageData = $imageData['image'];
                }else{
                    return false;
                }
            }
            // Завершение замера времени.
            ApiTimer::end($timerId, $timerId);
            $model = new static;
            $model->files = $imageData['files'];
            foreach ($model->files as $file) {
                if($file['type'] == 'mp4') {
                    $model->src = $file['href'];
                    break;
                }elseif( $file['type'] == 'webp'){
                    $model->src = $file['href'];
                }
            }
            $model->alt = $imageData['alt'];
            $model->width = $imageData['width'];
            $model->height = $imageData['height'];
            $model->category = $imageData['category'];
            $model->subCategory = $imageData['subCategory'];
            $model->href = self::renderLink($imageData);
            // Добавляем данные JSON-LD для этого изображения/видео
            $jsonLdData[] = self::generateJsonLdData($model);
            self::$image = $model;
            self::$image['jsonLdData'] = $jsonLdData;
        }

        return self::$image;
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
    private static function isVideo($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'mp4';
    }
    private static function generateJsonLdData($model) {
        $jsonData = [
            "@context" => "http://schema.org",
            "description" => $model->alt,
            "url" => $model->href,
            "associatedMedia" => []
        ];

        foreach ($model->files as $file) {
            $mediaType = self::isVideo($file['href']) ? 'VideoObject' : 'ImageObject';
            $jsonData['associatedMedia'][] = [
                "@type" => $mediaType,
                "contentUrl" => $file['href'],
                "width" => $model->width,
                "height" => $model->height
                // Здесь можно добавить другие свойства ImageObject или VideoObject, если они доступны
            ];
        }

        return $jsonData;
    }
}
