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
        'gpt/35turbo' => 'gpt/35turbo',
        'gpt/35turbo-send' => 'gpt/35turbo-send',
        'gpt/gemini' => 'gpt/gemini',
        'user/subscribe' => 'user/subscribe',
        'user/unsubscribe' => 'user/unsubscribe',
        'user/create-collection' => 'user/create-collection',
        'user/delete-collection' => 'user/delete-collection',
        'user/upload-to-server' => 'user/upload-to-server',
        'user/upload-to-cloud' => 'user/upload-to-cloud',
        'user/delete-from-cloud' => 'user/delete-from-cloud',
        'user/delete-local-file' => 'user/delete-local-file',
        'user/get-collections' => 'user/get-collections',
        'user/get-categories' => 'user/get-categories',
        'user/create-category' => 'user/create-category',
        'user/add-postcard' => 'user/add-postcard',
        'user/post-card-data' => 'user/post-card-data',
        'user/save-postcard' => 'user/save-postcard',
        'user/move-postcard' => 'user/move-postcard',
        'user/delete-postcard' => 'user/delete-postcard',
        'login' => 'pages/login',
        'signup' => 'pages/signup',
        'logout' => 'pages/logout',
        'auth' => 'pages/auth',
        'search' => 'pages/search',
        'profile/settings' => 'profile/settings',
        'profile/update-avatar' => 'profile/update-avatar',
        'register/validate' => 'pages/register-validate',
        'reset-password' => 'pages/reset-password',
        'confirm-email' => 'pages/confirm-email',
        'restore' => 'pages/restore',
        'page/<page:\d+>' => '/base/home',
        'base' => 'pages/base',
        '<username:\w+>/card/<hash:[\w_\/-]+>' => 'user/card',
        '<username:\w+>/collection/<id:\d+>' => 'user/collection',
        '<username:\w+>/category/<id:\d+>' => 'user/category',
        '<username:\w+>' => 'user/view',
        [
            'class' => 'yii\web\UrlRule',
            'pattern' => '<base:[\w_\/-]+>card-<hash>',
            'route' => 'pages/card',
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
        '404' => 'site/error',
    ],


];