<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class CategoryWidget extends Widget
{
    public $categories;

    public function run()
    {
        $output = '<p class="lead main-categories">';
        foreach ($this->categories as $category) {
            $output .= Html::a(
                $category->name . '<span class="badge bg-secondary">' . $category->count . '</span>',
                $category->id == 0 ? '/' : '/'.$category->slug,
                [
                    'class' => $category->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm',
                    'title' => $category->name . ' (' . $category->count . ')',
                ]
            );
        }
        $output .= '</p>';

        foreach ($this->categories as $category) {
            if ($category->isActive) {
                if ($category->alphabet) {
                    $output .= $this->renderAlphabeticalTabs($category);
                } else {
                    $output .= $this->renderNormalSubcategories($category);
                }
            }
        }
        $output = '<hr class="my-2">'. $output.'<hr class="my-2">';
        return $output;
    }

    private function renderNormalSubcategories($category)
    {
        $output = '<p class="lead sub-categories">';
        foreach ($category->subCategories as $subCategory) {
            $output .= Html::a(
                $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                '/'.$category->slug.'/'.$subCategory->slug,
                [
                    'class' => $subCategory->isActive ? 'btn btn-info btn-sm' : 'btn btn-secondary btn-sm',
                    'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                ]
            );
        }
        $output .= '</p>';
        return $output;
    }

    private function renderAlphabeticalTabs($category)
    {
        $alphabet = $this->getAlphabet();
        $output = '<ul class="nav nav-tabs" role="tablist" id="alphabetList">';
        $tabContents = "";

        foreach ($alphabet as $letter) {
            $isActiveTab = false;
            $tabContent = "";
            foreach ($category->subCategories as $subCategory) {
                if (mb_strtoupper(mb_substr($subCategory->name, 0, 1)) == $letter) {
                    $tabContent .= Html::a(
                        $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                        '/'.$category->slug.'/'.$subCategory->slug,
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
}
