<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\gpt\GPT35Turbo;

class GptController extends Controller
{
    public function action35turbo()
    {

        return $this->render('35turbo');
    }
    public function action35turboSend()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $content = Yii::$app->request->post('content');

        // Создайте экземпляр модели GPT35Turbo
        $gpt35Turbo = new \app\models\gpt\GPT35Turbo();
        $gpt35Turbo->apiKey = 'YOUR_API_KEY';
        $gpt35Turbo->prompt = $content;

        // Вызовите метод generateText для обработки данных
        $generatedText = $gpt35Turbo->generateText();

        if ($generatedText !== null) {
            // Верните успешный ответ с сгенерированным текстом
            return [
                'success' => true,
                'message' => 'Текст успешно сгенерирован.',
                'generatedText' => $generatedText,
            ];
        } else {
            // Если произошла ошибка при генерации текста, верните сообщение об ошибке
            return [
                'success' => false,
                'message' => 'Ошибка при генерации текста.',
            ];
        }
    }

}
