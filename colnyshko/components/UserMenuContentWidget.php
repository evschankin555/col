<?php

namespace app\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

class UserMenuContentWidget extends Widget
{
    public function run()
    {
        $menuItems = [
            ['label' => 'Мои коллекции', 'url' => 'http://localhost/'. Html::encode(Yii::$app->user->identity->username)],
            'divider',
            ['label' => 'Подписки', 'url' => '/profile/subscriptions'],
            ['label' => 'Подписчики', 'url' => '/profile/followers'],
            'divider',
            ['label' => 'Настройки профиля', 'url' => '/profile/settings'],
            ['label' => 'Выйти', 'url' => '/logout'],
            'divider',
        ];


        $items = [];
        foreach ($menuItems as $item) {
            if ($item == 'divider') {
                $items[] = Html::tag('div', '', ['class' => 'dropdown-divider']);
            } else {
                $items[] = Html::a($item['label'], $item['url'], ['class' => 'list-group-item list-group-item-action']);
            }
        }

        $listGroup = Html::tag('div', implode("\n", $items), ['class' => 'list-group']);

        $userAvatar = Html::img(Yii::$app->user->identity->avatarUrl, ['class' => 'd-block user-select-none', 'width' => '100%', 'height' => '200']);
        $userName = Html::tag('h3', Yii::$app->user->identity->username, ['class' => 'card-header']);

        echo Html::beginTag('div');

        echo Html::tag('div',
            Html::tag('div',
                Html::tag('div',
                    Html::tag('h5', 'Меню', ['class' => 'offcanvas-title', 'id' => 'offcanvasUserMenuLabel']) .
                    Html::button('', ['type' => 'button', 'class' => 'btn-close text-reset', 'data-bs-dismiss' => 'offcanvas', 'aria-label' => 'Close']),
                    ['class' => 'offcanvas-header']) .
                Html::tag('div',
                    Html::tag('div',
                        $userName .
                        Html::tag('div', $userAvatar, ['class' => 'card-body']) .
                        $listGroup,
                        ['class' => 'card']),
                    ['class' => 'offcanvas-body']
                ),
                ['class' => 'offcanvas offcanvas-end', 'tabindex' => '-1', 'id' => 'offcanvasUserMenu', 'aria-labelledby' => 'offcanvasUserMenuLabel']
            )
        );

        echo Html::endTag('div');
    }
}

