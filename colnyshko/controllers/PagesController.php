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
use app\models\Image;

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

        $currentCategory = false;
        $currentSubCategory = false;
        foreach ($categories as $cat) {
            if ($cat->slug == $category) {
                $currentCategory = $cat;
                break;
            }
        }

        $page = Yii::$app->request->get('page', 1);
        $imagesData = Images::getAll($page, $category);

        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages, $category);

        if ($currentCategory !== false) {
            // Если мы не на первой странице, то добавляем категорию как ссылку
            if ($page != 1) {
                $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name, 'url' => '/' . $currentCategory->slug];
                $this->view->params['breadcrumbs'][] = ['label' => 'Страница ' . $page];
            } else {
                $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name];
            }
        }

        return $this->render('category', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
            'currentCategory' => $currentCategory,
            'currentSubCategory' => $currentSubCategory,
        ]);
    }
    public function actionSubcategory($category, $subcategory)
    {
        $categories = Category::getAll();
        Category::setActiveSubCategory($subcategory);

        $currentCategory = false;
        $currentSubCategory = false;
        foreach ($categories as $cat) {
            if ($cat->slug == $category) {
                $currentCategory = $cat;
                foreach ($cat->subCategories as $subcat) {
                    if ($subcat->slug == $subcategory) {
                        $currentSubCategory = $subcat;
                        break;
                    }
                }
            }
            if ($currentSubCategory !== false) {
                break;
            }
        }

        $page = Yii::$app->request->get('page', 1);
        $imagesData = Images::getAll($page, null, $subcategory);

        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages, $category, $subcategory);

        if ($currentCategory !== false) {
            $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name, 'url' => '/' . $currentCategory->slug];
        }

        if ($currentSubCategory !== false) {
            // Если мы не на первой странице, то добавляем подкатегорию как ссылку
            if ($page != 1) {
                $this->view->params['breadcrumbs'][] = ['label' => $currentSubCategory->name, 'url' => '/' . $currentCategory->slug . '/' . $currentSubCategory->slug];
                $this->view->params['breadcrumbs'][] = ['label' => 'Страница ' . $page];
            } else {
                $this->view->params['breadcrumbs'][] = ['label' => $currentSubCategory->name];
            }
        }

        return $this->render('category', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
            'currentCategory' => $currentCategory,
            'currentSubCategory' => $currentSubCategory,
        ]);
    }
    public function actionCard($base, $hash)
    {
        $explode = explode('/', $base);
        $category = $explode[0];
        $subcategory = $explode[1];
        $image = Image::get($hash);
        $categories = Category::getAll();
        Category::setActiveSubCategory($subcategory);

        $currentCategory = null;
        $currentSubCategory = null;
        foreach ($categories as $cat) {
            if ($cat->slug == $category) {
                $currentCategory = $cat;
                foreach ($cat->subCategories as $subcat) {
                    if ($subcat->slug == $subcategory) {
                        $currentSubCategory = $subcat;
                        break;
                    }
                }
            }
            if ($currentSubCategory !== null) {
                break;
            }
        }
        if ($currentCategory !== null) {
            $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name, 'url' => '/' . $currentCategory->slug];
        }

        if ($currentSubCategory !== null) {
            $this->view->params['breadcrumbs'][] = ['label' => $currentSubCategory->name, 'url' => '/' . $currentCategory->slug . '/' . $currentSubCategory->slug];
            $this->view->params['breadcrumbs'][] = ['label' => $image->alt];
        }

        return $this->render('card', [
            'image' => $image,
            'categories' => $categories,
        ]);

    }
    public function actionBase()
    {
          return $this->render('base', []);
    }



}