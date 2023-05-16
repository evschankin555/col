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

use app\models\Images;
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

        $page = Yii::$app->request->get('page', 1);
        $imagesData = Images::getAll($page, $category);

        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages, $category);

        return $this->render('category', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
        ]);
    }
    public function actionSubcategory($category, $subcategory)
    {
        $categories = Category::getAll();
        Category::setActiveSubCategory($subcategory);

        $page = Yii::$app->request->get('page', 1);
        $imagesData = Images::getAll($page, null, $subcategory);

        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages, $category, $subcategory);

        return $this->render('category', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
        ]);
    }



}