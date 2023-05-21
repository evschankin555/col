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
            $model->href = $imageData['href'];
            self::$image = $model;
        }

        return self::$image;
    }
}
