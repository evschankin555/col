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
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '404',
            'route' => 'pages/404',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>/<subcategory>/',
            'route' => 'pages/subcategory',
            'suffix' => ''
        ],
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<category:[\w_\/-]+>',
            'route' => 'pages/category',
            'suffix' => ''
        ],



    ],
];