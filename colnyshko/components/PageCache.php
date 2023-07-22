<?php
namespace app\components;
use Yii;
class PageCache {

    private static $cache;
    private static $key;

    public static function start($id) {
        self::$cache = Yii::$app->cache;

        // Определяем уникальный ключ кеша
        self::$key = 'page-cache-18-' . $id;

        $html = self::$cache->get(self::$key);

        // Если кешированная версия найдена, возвращаем её
        if ($html !== false) {
            return $html;
        }

        return null;
    }

    public static function end($html) {
        // Сохраняем вывод в кеш
        self::$cache->set(self::$key, $html, 86400); // Кеширование на 24 часа, можно менять в соответствии с потребностями

        // Возвращаем HTML страницы
        return $html;
    }
}
