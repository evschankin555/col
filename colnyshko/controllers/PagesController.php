<?php

namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SearchForm;
use app\models\Category;

class PagesController extends BaseController
{

    public $enableCsrfValidation = false;
    /**
     * @param $url
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCategory($category)
    {
        $categories = Category::getAll();
        Category::setActive($category);

        return $this->render('category', [
            'categories' => $categories,
        ]);
    }
    public function actionSubcategory($category, $subcategory)
    {
        $categories = Category::getAll();
        Category::setActiveSubCategory($subcategory);

        return $this->render('category', [
            'categories' => $categories,
        ]);
    }

}