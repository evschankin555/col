<?php
namespace app\models;

use GuzzleHttp\Client;
use Yii;
use yii\base\Model;

class Image extends Model
{
    public $src;
    public $alt;
    public $href;

    private static $image;

    public static function get($hash)
    {
        if (self::$image === null) {
            $client = new Client(['base_uri' => 'https://legkie-otkrytki.ru/api/']);

            $cache = Yii::$app->cache;
            $cacheKey = "image_new_{$hash}";
            $imageData = $cache->get($cacheKey);

            if ($imageData === false) {
                $response = $client->request('GET', 'image', ['query' => ['hash' => $hash]]);
                $imageData = json_decode($response->getBody(), true);
                if($imageData['result'] === 'success') {
                    $cache->set($cacheKey, $imageData['image'], 300);
                    $imageData = $imageData['image'];
                }else{
                    return false;
                }
            }
            $model = new static;
            $model->src = $imageData['src'];
            $model->alt = $imageData['alt'];
            $model->href = self::renderLink($imageData);
            self::$image = $model;
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

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        $string = strtr($string, $converter); // Транслитерация
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
}
