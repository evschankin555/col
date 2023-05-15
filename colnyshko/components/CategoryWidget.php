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
                $output .= '<p class="lead sub-categories">';
                foreach ($category->subCategories as $subCategory) {
                    $output .= Html::a(
                        $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                        '/'.$category->slug.'/'.$subCategory->slug,
                        [
                            'class' => $subCategory->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm',
                            'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                        ]
                    );
                }
                $output .= '</p>';
            }
        }

        return $output;
    }
}
