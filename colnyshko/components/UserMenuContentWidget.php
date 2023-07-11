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
            ['label' => 'Каталог', 'url' => '/'],
            ['label' => 'Моя страница', 'url' => '/user-panel/my-page'],
            ['label' => 'Мои друзья', 'url' => '/user-panel/my-friends'],
            'divider',
            ['label' => 'Сохраненные подборки', 'url' => '/user-panel/saved-collections'],
            ['label' => 'Любимые открытки', 'url' => '/user-panel/favorite-cards'],
            ['label' => 'Закладки', 'url' => '/user-panel/bookmarks'],
            'divider',
            ['label' => 'Мои подборки', 'url' => '/user-panel/my-collections'],
            ['label' => 'Мои Персонализированные открытки', 'url' => '/user-panel/personalized-cards'],
            'divider',
            ['label' => 'Мои загрузки', 'url' => '/user-panel/my-uploads'],
            ['label' => 'Музыкальные треки', 'url' => '/user-panel/music-tracks'],
            ['label' => 'Мои текстовые пожелания', 'url' => '/user-panel/my-text-wishes'],
            'divider',
            ['label' => 'Мои события', 'url' => '/user-panel/my-events'],
            ['label' => 'События моих друзей', 'url' => '/user-panel/friends-events'],
            ['label' => 'Настройки профиля', 'url' => '/user-panel/profile-settings'],

            'divider',
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

