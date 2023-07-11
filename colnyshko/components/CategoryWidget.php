<?php
namespace app\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

class CategoryWidget extends Widget
{
    public $categories;
    public $filters = true;

    public function run()
    {
        // Получаем параметр display из URL
        $display = Yii::$app->request->getQueryParam('display', 'all');
        $sort = Yii::$app->request->getQueryParam('sort', 'new');
        $output = '<p class="lead main-categories">';
        foreach ($this->categories as $category) {
            $url = $this->generateUrl($category->id == 0 ? '/' : '/' . $category->slug, $display, $sort);

            $output .= Html::a(
                $category->name . '<span class="badge bg-secondary">' . $category->count . '</span>',
                $url,
                [
                    'class' => $category->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm',
                    'title' => $category->name . ' (' . $category->count . ')',
                ]
            );
        }
        $output .= '</p>';

        foreach ($this->categories as $category) {
            if ($category->isActive) {
                // Проверка для каждой подкатегории, чтобы определить, какой метод вызвать
                $hasMonth = false;
                foreach ($category->subCategories as $subCategory) {
                    if ($subCategory->month > 0) {
                        $hasMonth = true;
                        break;
                    }
                }

                if ($category->alphabet) {
                    $output .= $this->renderAlphabeticalTabs($category, $display, $sort);
                } elseif ($hasMonth) {
                    $output .= $this->renderMonthlyTabs($category, $display, $sort);
                } else {
                    $output .= $this->renderNormalSubcategories($category, $display, $sort);
                }
            }
        }
        if($this->filters) {
            $output .= '
    <div class="filters-wrapper">
        ' . $this->renderDisplayFilter($display) . '
       
    </div>
';// ' . $this->renderSortFilter($sort) . '

        }
        $output = '<hr class="my-2">'. $output.'<hr class="my-2">';
        return $output;
    }

    private function renderDisplayFilter($display)
    {

        $displayValues = [
            'all' => 'Все',
            'animations' => 'Только анимации',
            'static' => 'Только статичные'
        ];

        $buttonClasses = [
            'all' => 'btn-secondary',
            'animations' => 'btn-primary',
            'static' => 'btn-danger'
        ];

        return '<div class="btn-group display-buttons" role="group" aria-label="Button group with nested dropdown">
            <button id="displayButton" type="button" class="btn '.$buttonClasses[$display].' btn-sm">Отобразить: '.$displayValues[$display].'</button>
            <div class="btn-group" role="group">
                <button id="dropdownButton" type="button" class="btn '.$buttonClasses[$display].' btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop3" style="">
                    <a class="dropdown-item" data-value="all">Все</a>
                    <a class="dropdown-item" data-value="animations">Только анимации</a>
                    <a class="dropdown-item" data-value="static">Только статичные</a>
                </div>
            </div>
        </div>';
    }
    private function renderSortFilter($sort)
    {
        $sortValues = [
            'new' => 'Новые',
            'popular' => 'Популярные',
        ];


        return '<div class="btn-group sort-buttons" role="group" aria-label="Button group with nested dropdown">
        <button id="sortButton" type="button" class="btn btn-secondary btn-sm">Сортировать: '.$sortValues[$sort].'</button>
        <div class="btn-group" role="group">
            <button id="dropdownButton" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop2" style="">
                <a class="dropdown-item" data-value="new">Новые</a>
                <a class="dropdown-item" data-value="popular">Популярные</a>
            </div>
        </div>
    </div>';
    }


    private function renderNormalSubcategories($category, $display, $sort)
    {
        $output = '<p class="lead sub-categories">';
        foreach ($category->subCategories as $subCategory) {
            $url = $this->generateUrl('/'.$category->slug.'/'.$subCategory->slug, $display, $sort);
            $output .= Html::a(
                $this->processName($subCategory->name) . '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                $url,
                [
                    'class' => $subCategory->isActive ? 'btn btn-info btn-sm' : 'btn btn-secondary btn-sm',
                    'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                ]
            );
        }
        $output .= '</p>';
        return $output;
    }

    private function renderAlphabeticalTabs($category, $display, $sort)
    {
        $alphabet = $this->getAlphabet();
        $output = '<ul class="nav nav-tabs" role="tablist" id="alphabetList">';
        $tabContents = "";

        foreach ($alphabet as $letter) {
            $isActiveTab = false;
            $tabContent = "";
            foreach ($category->subCategories as $subCategory) {
                if (mb_strtoupper(mb_substr($subCategory->name, 0, 1)) == $letter) {
                    $url = $this->generateUrl('/'.$category->slug.'/'.$subCategory->slug, $display, $sort);
                    $tabContent .= Html::a(
                        $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                        $url,
                        [
                            'class' => $subCategory->isActive ? 'btn btn-info btn-sm' : 'btn btn-secondary btn-sm',
                            'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                        ]
                    );
                    if ($subCategory->isActive) {
                        $isActiveTab = true;
                    }
                }
            }

            if ($tabContent != "") {
                $output .= '<li class="nav-item" role="presentation">';
                $output .= '<a class="nav-link '. ($isActiveTab ? 'active' : '') .'" data-bs-toggle="tab" href="#'.$letter.'" role="tab">'.$letter.'</a>';
                $output .= '</li>';

                $tabContents .= '<div class="tab-pane fade '. ($isActiveTab ? 'show active' : '') .'" id="'.$letter.'" role="tabpanel"><p>';
                $tabContents .= $tabContent;
                $tabContents .= '</p></div>';
            }
        }

        $output .= '</ul>';
        $output .= '<div id="alphabetTabContent" class="tab-content">' . $tabContents . '</div>';
        return $output;
    }
    private function getAlphabet()
    {
        return [
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
            'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
            'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Э', 'Ю', 'Я'
        ];
    }
    private static function processName($name)
    {
        if (strpos($name, '-') !== false) {
            $nameParts = explode('-', $name);
            return trim($nameParts[0]);
        } else {
            return $name;
        }
    }
    private function getMonths()
    {
        return [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];
    }

    private function renderMonthlyTabs($category, $display, $sort)
    {
        $months = $this->getMonths();
        $output = '<ul class="nav nav-tabs" role="tablist" id="monthList">';
        $tabContents = "";

        foreach ($months as $monthNumber => $monthName) {
            $isActiveTab = false;
            $tabContent = "";
            foreach ($category->subCategories as $subCategory) {
                if ($subCategory->month == $monthNumber) {
                    $url = $this->generateUrl('/'.$category->slug.'/'.$subCategory->slug, $display, $sort);
                    $tabContent .= Html::a(
                        $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                        $url,
                        [
                            'class' => $subCategory->isActive ? 'btn btn-info btn-sm' : 'btn btn-secondary btn-sm',
                            'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                        ]
                    );
                    if ($subCategory->isActive) {
                        $isActiveTab = true;
                    }
                }
            }

            if ($tabContent != "") {
                $output .= '<li class="nav-item" role="presentation">';
                $output .= '<a class="nav-link '. ($isActiveTab ? 'active' : '') .'" data-bs-toggle="tab" href="#'.$monthName.'" role="tab">'.$monthName.'</a>';
                $output .= '</li>';

                $tabContents .= '<div class="tab-pane fade '. ($isActiveTab ? 'show active' : '') .'" id="'.$monthName.'" role="tabpanel"><p>';
                $tabContents .= $tabContent;
                $tabContents .= '</p></div>';
            }
        }

        $output .= '</ul>';
        $output .= '<div id="monthTabContent" class="tab-content">' . $tabContents . '</div>';
        return $output;
    }
    private function generateUrl($baseUrl, $display = 'all', $sort = 'new')
    {
        $url = $baseUrl;

        if ($display != 'all' || $sort != 'new') {
            $url .= '?';

            if ($display != 'all') {
                $url .= 'display=' . $display;
            }

            if ($sort != 'new') {
                // добавляем амперсанд, если оба параметра присутствуют
                if ($display != 'all') {
                    $url .= '&';
                }

                $url .= 'sort=' . $sort;
            }
        }

        return $url;
    }

}
