<?php
return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => false,

    'normalizer' => [
        'class' => 'yii\web\UrlNormalizer',
        'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_PERMANENT,
    ],

    'rules' => [
        '/' => '/base/home',
        'page/<page:\d+>' => '/base/home',
        'login' => 'site/login',
        'logout' => 'site/logout',
        'contact' => 'site/contact',
        'about' => 'site/about',
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '404',
            'route' => 'site/error', // соответствует действию actionError в SiteController
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'base',
            'route' => 'pages/base', // соответствует действию actionError в SiteController
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>/<subcategory>/page/<page:\d+>',
            'route' => 'pages/subcategory',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>/page/<page:\d+>',
            'route' => 'pages/category',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>/<subcategory>',
            'route' => 'pages/subcategory',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>',
            'route' => 'pages/category',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'register',
            'route' => 'pages/register',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'auth',
            'route' => 'pages/auth',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'logout',
            'route' => 'pages/logout',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'confirm-email',
            'route' => 'pages/confirm-email',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'restore',
            'route' => 'pages/restore',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => 'en/reset-password',
            'route' => 'pages/reset-password',
            'suffix' => ''
        ],
    ],

];