<?php

namespace app\controllers;

use Yii;
use app\models\ProfileForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\User;

class UserController extends Controller{
    public function actionView($username)
    {
        $model = User::find()->where(['username' => $username])->one();

        if ($model === null) {
            throw new NotFoundHttpException("The user was not found.");
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
