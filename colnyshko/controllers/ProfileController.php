<?php

namespace app\controllers;

use Yii;
use app\models\ProfileForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['settings'],
                'rules' => [
                    [
                        'actions' => ['settings'],
                        'allow' => true,
                        'roles' => ['@'],  // доступ только для аутентифицированных пользователей
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'settings' => ['get', 'post'],
                ],
            ],
        ];
    }

    public function actionSettings()
    {
        $model = new ProfileForm();
        $model->loadCurrent(Yii::$app->user->identity);

        if ($model->load(Yii::$app->request->post()) && $model->updateProfile()) {
            return $this->redirect(['profile/settings']);
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAvatar()
    {
        if (Yii::$app->request->isAjax) {
            $model = new ProfileForm();
            $model->loadCurrent(Yii::$app->user->identity);
            $model->avatar = Yii::$app->request->post('avatar');

            if ($model->updateProfile()) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true];
            }
        }

        throw new \yii\web\BadRequestHttpException('Invalid request.');
    }

}
