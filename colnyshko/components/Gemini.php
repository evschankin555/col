<?php


namespace app\components;

use yii\base\Component;
use yii\httpclient\Client;

class Gemini extends Component
{
    public $apiKey;

    public function init()
    {
        parent::init();

        if (empty($this->apiKey)) {
            throw new InvalidConfigException('The "apiKey" property must be set.');
        }
    }

    public function generateContent($query)
    {
        $client = new Client();

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $this->apiKey;

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $query
                        ]
                    ]
                ]
            ]
        ];

        $options = [
            'http' => [
                'header' => [
                    'Content-Type: application/json'
                ],
                'method' => 'POST',
                'content' => json_encode($data),
                'timeout' => 30 // Увеличено время ожидания до 30 секунд
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result !== false) {
            $result = json_decode($result, true);
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            throw new \Exception('API request failed');
        }
    }

}
