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

    public function actionRegister()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $this->layout = 'json';
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = new User();
        $userReg = $user->register($username, $email, $password);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');

        if ($userReg === true) {
            Yii::$app->response->data = ['success' => true];
        } else {
            // Формируем ответ с ошибками
            $response = ['success' => false];
            foreach ($userReg as $attribute => $errors) {
                $response['errors'][$attribute] = implode(', ', $errors);
            }
            Yii::$app->response->data = $response;
        }
    }


    public function actionAuth()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userModel = new User();
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userAuth = $userModel->login($email, $password);

        if ($userAuth) {
            return ['success' => true];
        } else {
            return [
                'success' => false,
                'error' => 'Incorrect email/password combination',
            ];
        }
    }
    public function actionRestore()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userModel = new User();
        $email = $_POST['email'];
        $isRestore = $userModel->restore($email);

        if ($isRestore) {
            return ['success' => true];
        } else {
            return [
                'success' => false,
                'error' => "This email address does not exist."
            ];
        }
    }

    public function actionConfirmEmail()
    {
        $userModel = new User();
        $code = $_GET['code'];

        $confirmedUser = $userModel->confirmEmail($code);

        if ($confirmedUser !== null) {
            Yii::$app->user->login($confirmedUser);
            $email = $confirmedUser->email;
            $isConfirm = true;
        } else {
            $email = null;
            $isConfirm = false;
        }

        return $this->render('confirm-email', [
            'isConfirm' => $isConfirm,
            'email' => $email,
        ]);
    }


    public function actionResetPassword()
    {
        $userModel = new User();
        $code = $_GET['token'];
        if(isset($_POST['code'])){
            $code = $_POST['code'];
        }
        $user = $userModel->findUserByResetCode($code);
        if ($user !== null) {
            $isReset = true;

            if (isset($_POST['code']) && isset($_POST['password'])) {
                $newPassword = $_POST['password'];
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($newPassword);
                $user->confirmation_code = '';
                $user->save();

                Yii::$app->user->login($user);
                Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                return ['success' => true];
            }

        } else {
            $isReset = false;
        }
        return $this->render('reset-password', [
            'isReset' => $isReset,
            'model' => $userModel,
            'code' => $code
        ]);
    }



    public function actionLogout()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $user = new User();
        $userLogout = $user->logout();
        if ($userLogout) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Error logout'];
        }
    }

}