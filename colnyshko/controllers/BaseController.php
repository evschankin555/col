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
        $categories = Category::getAll();
        Category::setActive(0);

        return $this->render('home', [
            'categories' => $categories,
        ]);
    }
}