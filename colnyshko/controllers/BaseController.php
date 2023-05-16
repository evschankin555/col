<?php

namespace app\controllers;

use app\components\Common;
use app\models\Category;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\web\Controller;
use app\models\ContactForm;
use app\models\SubmitFeed;
use Yii;
use app\models\User;
use app\models\SourceList;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\models\Images;

class BaseController extends Controller
{

    public $enableCsrfValidation = false;
    public function beforeAction($action)
{
    $this->enableCsrfValidation = false;

    return parent :: beforeAction($action);
}
    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionHome(): string
    {

        $this->view->params['title'] = 'Солнышко';
        $categories = Category::getAll();
        Category::setActive(0);

        $page = Yii::$app->request->get('page', 1);
        $imagesData = Images::getAll($page);

        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages);

        return $this->render('home', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
        ]);
    }


}