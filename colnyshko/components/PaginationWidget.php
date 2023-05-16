<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class PaginationWidget extends Widget
{
    public $pagination;

    public function run()
    {
        $output = '<ul class="pagination pagination-lg">';

        foreach ($this->pagination as $pageItem) {
            $output .= $this->renderPageItem($pageItem);
        }

        $output .= '</ul>';

        return $output;
    }

    private function renderPageItem($pageItem)
    {
        $class = 'page-item';
        if ($pageItem['disabled']) {
            $class .= ' disabled';
        }
        if ($pageItem['active']) {
            $class .= ' active';
        }

        $url = $pageItem['url'];
        $output = '<li class="' . $class . '">';
        $output .= Html::a($pageItem['label'], $url, ['class' => $pageItem['label'] == '...' ? 'page-link page-dotted' : 'page-link']);
        $output .= '</li>';

        return $output;
    }


}