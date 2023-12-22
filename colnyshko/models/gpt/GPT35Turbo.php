<?php


namespace app\models\gpt;

use yii\base\Model;
use yii\helpers\Json;

class GPT35Turbo extends Model
{
    public $apiKey;
    public $endpoint = 'https://neuroapi.host/gpt4/v1/chat/completions';

    public $prompt;
    public $max_tokens = 128;

    public function rules()
    {
        return [
            [['apiKey', 'prompt'], 'required'],
        ];
    }

    /**
     * Метод для генерации текста с использованием GPT-3.5 Turbo.
     *
     * @return string|null Сгенерированный текст или null в случае ошибки.
     */
    public function generateText()
    {
        if ($this->validate()) {
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ];

            $data = [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->prompt
                    ]
                ],
                'max_tokens' => $this->max_tokens,
                'temperature' => 0.9,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'supports_stream' => 0,
                'presence_penalty' => 0
            ];

            $ch = curl_init($this->endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            if ($response !== false) {
                return $data;
            }
        }

        return null;
    }

}
