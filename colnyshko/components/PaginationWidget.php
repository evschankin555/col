<?php

namespace app\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class PaginationWidget extends Widget
{
    public $pagination;
    public function run()
    {
        if (count($this->pagination) == 0) {
            return '';
        }
        $output = '<hr class="my-2"><ul class="pagination pagination-lg">';
        foreach ($this->pagination as $pageItem) {
            $output .= $this->renderPageItem($pageItem);
        }
        $output .= '</ul><hr class="my-2">';
        return $output;
    }

    private function renderPageItem($pageItem)
    {
        $class = 'page-item';
        if($pageItem['label'] == '«') {
            $class .= ' arrow-left';
            $pageItem['label'] = '&nbsp;';
        }
        if($pageItem['label'] == '»') {
            $class .= ' arrow-right';
            $pageItem['label'] = '&nbsp;';
        }
        if ($pageItem['disabled']) {
            $class .= ' disabled';
        }
        if ($pageItem['active']) {
            $class .= ' active';
        }
        $url = $pageItem['url'];

        // Получаем параметры текущего запроса
        $queryParams = Yii::$app->request->getQueryParams();
        // Удаляем ненужные параметры, которые уже присутствуют в URL пагинации
        unset($queryParams['page'], $queryParams['category']);

        // Объединяем их с URL каждого элемента пагинации
        $urlWithParams = Url::to([$url] + $queryParams);

        $output = '<li class="' . $class . '">';
        $output .= Html::a($pageItem['label'], $urlWithParams, ['class' => $pageItem['label'] == '...' ? 'page-link page-dotted' : 'page-link']);
        $output .= '</li>';
        return $output;
    }
}