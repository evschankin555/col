<?php
namespace app\components;

class Base62Converter
{
    private static $base62chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    public static function encode($number)
    {
        $base = strlen(self::$base62chars);
        $encoded = '';

        while ($number > 0) {
            $remainder = $number % $base;
            $encoded = self::$base62chars[$remainder] . $encoded;
            $number = floor($number / $base);
        }

        return $encoded === '' ? '0' : $encoded;
    }

    public static function decode($string)
    {
        $base = strlen(self::$base62chars);
        $decoded = 0;

        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $pos = strpos(self::$base62chars, $string[$i]);
            $decoded += $pos * pow($base, $length - $i - 1);
        }

        return $decoded;
    }
}
