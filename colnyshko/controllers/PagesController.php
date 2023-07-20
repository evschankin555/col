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
use app\modules\SeoModule;
use app\models\User;
use app\models\SignupForm;
use app\models\PasswordRecoveryForm;

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
        $display = Yii::$app->request->get('display');
        $categories = Category::getAll($display);
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
        $sort = Yii::$app->request->get('sort');
        $imagesData = Images::getAll($page, $category, null, $display, $sort);


        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages, $category);

        if ($currentCategory !== false) {
            // Если мы не на первой странице, то добавляем категорию как ссылку
            if ($page != 1) {
                $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name, 'url' => '/' . $currentCategory->slug];
                $this->view->params['breadcrumbs'][] = ['label' => 'Страница ' . $page];
                $this->view->title = $currentCategory->name . ' - Страница ' . $page;
            } else {
                $this->view->params['breadcrumbs'][] = ['label' => $currentCategory->name];
                $this->view->title = $currentCategory->name;
            }
        }

        $homeData = [];
        $homeData['name'] = $this->view->title;

        $seoModule = \Yii::$app->getModule('seo-module');
        $seoModule->setCategoryPageData($homeData);
        $seoModule->registerSeoTags();
        $seoModule->registerJsonLdScript($imagesData['jsonLdData']);

        return $this->render('category', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
            'currentCategory' => $currentCategory,
            'currentSubCategory' => $currentSubCategory,
        ]);
    }
/**/
    public function actionSubcategory($category, $subcategory)
    {
        $display = Yii::$app->request->get('display');
        $categories = Category::getAll($display);
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
        $sort = Yii::$app->request->get('sort');
        $imagesData = Images::getAll($page, null, $subcategory, $display, $sort);

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
                $this->view->title = $currentSubCategory->name . ' - Страница ' . $page;
            } else {
                $this->view->params['breadcrumbs'][] = ['label' => $currentSubCategory->name];
                $this->view->title = $currentSubCategory->name;
            }
        }

        $homeData = [];
        $homeData['name'] = $this->view->title;

        $seoModule = \Yii::$app->getModule('seo-module');
        $seoModule->setSubcategoryPageData($homeData);
        $seoModule->registerSeoTags();
        $seoModule->registerJsonLdScript($imagesData['jsonLdData']);

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
        $display = Yii::$app->request->get('display');
        $explode = explode('/', $base);
        $category = $explode[0];
        $subcategory = $explode[1];
        $image = Image::get($hash);
        $categories = Category::getAll($display);
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

            $this->view->title = $image->alt;
        }

        $homeData = [];
        $homeData['name'] = $this->view->title;

        $seoModule = \Yii::$app->getModule('seo-module');
        $seoModule->setImageData($image->src, $image->width, $image->height);

        $seoModule->setImagePageData($homeData, $image);
        $seoModule->registerSeoTags();
        $seoModule->registerJsonLdScript($image['jsonLdData']);

        return $this->render('card', [
            'image' => $image,
            'categories' => $categories,
        ]);

    }
    public function actionBase()
    {
          return $this->render('base', []);
    }
    public function actionSearch($q): string
        {
            $page = Yii::$app->request->get('page', 1);
            $dopTitle = '';
            if($page >  1) {
                $dopTitle = ', страница '.$page;
            }

            $this->view->params['title'] = 'Поиск по фразе: '. $q.$dopTitle;

            if (!empty($q)) {
                $this->view->params['breadcrumbs'][] = ['label' => 'Поиск по фразе: "' . $q.'"'.$dopTitle];
            }

            $this->view->title = $this->view->params['title'];

            $imagesData = Images::search($q, $page);

            $images = $imagesData['images'];
            $totalPages = $imagesData['pages'];

            $pagination = Images::getPagination($page, $totalPages, $categorySlug = null, $subCategorySlug = null, $q);

            $homeData = [];
            $homeData['query'] = $q.$dopTitle;

            $seoModule = \Yii::$app->getModule('seo-module');
            $seoModule->setSearchPageData($homeData);
            $seoModule->registerSeoTags();
            $seoModule->registerJsonLdScript($imagesData['jsonLdData']);

            return $this->render('search', [
                'images' => $images,
                'pagination' => $pagination,
                'query' => $q
            ]);
        }
    public function actionLogin()
    {

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Замените goBack() на редирект на профиль пользователя
            return $this->redirect(['/'. Yii::$app->user->identity->username]);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        $user = new User();
        $userLogout = $user->logout();

        if ($userLogout) {
            // После успешного выхода перенаправляем пользователя на главную страницу
            return $this->goHome();
        } else {
            // Если выход не удался, можно перенаправить пользователя обратно на страницу, с которой он пришел,
            // или показать ему сообщение об ошибке
            Yii::$app->session->setFlash('error', 'Error logout');
            return $this->goBack();
        }
    }
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            return $this->redirect(['/'. Yii::$app->user->identity->username]);
        } else {
            return $this->render('signup', [
                'model' => $model,
            ]);
        }
    }

    public function actionRestore()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new PasswordRecoveryForm();
        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
            return $this->goHome();
        }

        return $this->render('restore', [
            'model' => $model,
        ]);
    }



}