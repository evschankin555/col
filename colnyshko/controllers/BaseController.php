<?php

namespace app\controllers;

use app\components\Common;
use app\models\Category;
use app\models\user_related\ImageRelation;
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
use app\modules\SeoModule;

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
    public function _actionHome(): string
    {

        $display = Yii::$app->request->get('display');
        $this->view->params['title'] = 'Солнышко';
        $categories = Category::getAll($display);
        Category::setActive(0);

        $page = Yii::$app->request->get('page', 1);
        $sort = Yii::$app->request->get('sort');
        $imagesData = Images::getAll($page, null, null, $display, $sort);


        $images = $imagesData['images'];
        $totalPages = $imagesData['pages'];

        $pagination = Images::getPagination($page, $totalPages);

        $homeData = [];
        $homeData['name'] = $this->view->params['title'];
        $seoModule = \Yii::$app->getModule('seo-module');
        $seoModule->setHomePageData($homeData);
        $seoModule->registerSeoTags();
        $seoModule->registerJsonLdScript($imagesData['jsonLdData']);

        return $this->render('home', [
            'categories' => $categories,
            'images' => $images,
            'pagination' => $pagination,
        ]);
    }
    public function actionHome(): string
    {
        $images = ImageRelation::getImagesNewHome();

        return $this->render('home', [
            'images' => $images
        ]);
    }
    public function actionRegister()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->headers->add('X-Ie-Redirect-Compatibility', '1');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $this->layout = 'json';
        $model = new User();

        // загрузка данных из запроса в модель и проверка на валидность
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // если данные прошли валидацию, производится регистрация пользователя
            $userReg = $model->register($model->username, $model->email, $model->password);

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
        } else {
            // если данные не прошли валидацию, возвращаем ошибки
            Yii::$app->response->data = ['success' => false, 'errors' => $model->errors];
        }
    }
    public function actionRegisterValidate()
    {
        $model = new User();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }
    public function actionAuth()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userModel = new User();
        $postData = Yii::$app->request->post('User');
        $email = isset($postData['email']) ? $postData['email'] : null;
        $password = isset($postData['password']) ? $postData['password'] : null;

        // Выход из метода, если какие-то из данных не переданы
        if ($email === null || $password === null) {
            Yii::$app->response->data = ['success' => false, 'error' => 'Обязательные поля пустые.'];
            return;
        }

        $userAuth = $userModel->login($email, $password);
        if ($userAuth) {
            return ['success' => true];
        } else {
            return [
                'success' => false,
                'error' => 'Не верный логин или пароль.'
            ];
        }
    }
    public function actionRestore()
    {
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userModel = new User();
        $email = Yii::$app->request->post('email');

        // Выход из метода, если какие-то из данных не переданы
        if ($email === null) {
            Yii::$app->response->data = ['success' => false, 'error' => 'Обязательные поля пустые.'];
            return;
        }

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