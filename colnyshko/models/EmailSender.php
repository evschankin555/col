<?php
namespace app\models;

use Yii;
use yii\base\Model;

class EmailSender extends Model
{
    public function sendConfirmationEmail($email, $confirmation_code)
    {
        $user = User::findOne(['email' => $email]);
        $confirmationLink = Yii::$app->urlManager->createAbsoluteUrl(['confirm-email', 'code' => $confirmation_code]);

        return Yii::$app->mailer->compose(['html' => 'confirmationEmail-html'], ['user' => $user, 'confirmationLink' => $confirmationLink])
            ->setFrom(['no-reply@gtfs.pro' => 'GTFS.pro'])
            ->setTo($email)
            ->setSubject('Registration confirmation on GTFS.pro')
            ->send();
    }

}
