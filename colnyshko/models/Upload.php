<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "uploads".
 *
 * @property int $id
 * @property int $user_id
 * @property string $file_name
 * @property string $uploaded_at
 * @property string $status
 */
class Upload extends ActiveRecord
{
    public static function tableName()
    {
        return 'uploads';
    }

    public function rules()
    {
        return [
            [['user_id', 'file_name', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['uploaded_at'], 'safe'],
            // Обновленные правила для статуса
            [['status'], 'in', 'range' => ['uploading', 'uploaded', 'transferred', 'cloud_uploaded', 'error']],
            [['file_name'], 'string', 'max' => 255],
            [['cloud_url'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file_name' => 'File Name',
            'uploaded_at' => 'Uploaded At',
            'status' => 'Status',
            'cloud_url' => 'Cloud URL',
        ];
    }
}
