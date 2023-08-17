<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use Aws\S3\S3Client;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var S3Client
     */
    protected $s3Client;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, jpeg, png, gif, mp4, webp'],
        ];
    }

    /**
     * Инициализация
     */
    public function init()
    {
        parent::init();
        $this->s3Client = $this->getS3Client();
    }

    /**
     * Возвращает экземпляр S3Client
     *
     * @return S3Client
     */
    protected function getS3Client()
    {
        return new S3Client([
            'version' => Yii::$app->params['s3client_version'],
            'region' => Yii::$app->params['s3client_region'],
            'endpoint' => Yii::$app->params['s3client_endpoint'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => Yii::$app->params['s3client_key'],
                'secret' => Yii::$app->params['s3client_secret'],
            ],
        ]);
    }

    /**
     * Загрузка файла на облако
     */
    public function uploadFileToCloud($user_id, $file_id)
    {
        $temp_file = Yii::getAlias('@webroot/../upload_images/') . $file_id;
        $file_extension = pathinfo($temp_file, PATHINFO_EXTENSION);
        $file_name_unique = $this->file_name_unique($user_id, $file_id) . '.' . $file_extension;
        $file_path = 'upload_images/' . $file_name_unique;

        if (!file_exists($temp_file)) {
            return false;
        }

        try {
            $result = $this->s3Client->putObject([
                'Bucket' => Yii::$app->params['s3client_bucket'],
                'Key' => $file_path,
                'Body' => fopen($temp_file, 'r'),
                'ACL' => 'public-read',
            ]);

            $file_url = $result['ObjectURL'];
        } catch (Aws\Exception\S3Exception $e) {
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            Yii::warning('Ошибка проверки файла');
            Yii::error("Произошла ошибка при загрузке файла: " . $e->getMessage());
            return false;
        }

        return $file_url;
    }

    /**
     * Удаление объекта из S3 по URL
     */
    public function deleteObjectFromCloud($fileUrl)
    {
        $parsedUrl = parse_url($fileUrl);
        $filePath = ltrim($parsedUrl['path'], '/');

        // Удалить имя бакета из пути
        $bucketName = Yii::$app->params['s3client_bucket'];
        if (strpos($filePath, $bucketName) === 0) {
            $filePath = substr($filePath, strlen($bucketName) + 1); // +1 для слеша
        }

        try {
            $result = $this->s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $filePath,
            ]);

            return $result;

        } catch (Aws\Exception\S3Exception $e) {
            Yii::warning('Ошибка при удалении файла из облака');
            Yii::error("Произошла ошибка при удалении файла: " . $e->getMessage());
            return false;
        }
    }



    public function deleteLocalFile($file_id)
    {
        $local_file_path = Yii::getAlias('@webroot/../upload_images/') . $file_id;
        if (file_exists($local_file_path)) {
            unlink($local_file_path);
            return true;
        } else {
            Yii::warning('Не удалось удалить локальный файл: ' . $local_file_path);
            return false;
        }
    }

    public function upload()
    {
        if (!$this->validate()) {
            Yii::error('Ошибка валидации файла: ' . json_encode($this->errors));
            return false;
        }
        if ($this->validate()) {
            $unique_name = uniqid() . '.' . $this->file->extension;
            $local_file_path = Yii::getAlias('@webroot/../upload_images/') . $unique_name;

            if ($this->file->saveAs($local_file_path)) {
                return [
                    'success' => true,
                    'file_id' => $unique_name,
                ];
            } else {
                Yii::error('Не удалось сохранить файл: ' . $local_file_path);
                return false;
            }
        } else {
            return false;
        }
    }

    public function file_name_unique($user_id, $name)
    {
        $hashed_user_id = substr(hash('md5', $user_id), 0, 6);
        $hashed_feed_name = substr(hash('sha256', $name), 0, 4);
        $hashed_time = substr(hash('md5', time()), 0, 4);
        return 'file-' . $hashed_user_id . '-' . $hashed_feed_name . '-' . $hashed_time;
    }
}

