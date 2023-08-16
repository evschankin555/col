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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uploads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'file_name', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['uploaded_at'], 'safe'],
            [['status'], 'in', 'range' => ['uploading', 'uploaded', 'transferred', 'error']],
            [['file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file_name' => 'File Name',
            'uploaded_at' => 'Uploaded At',
            'status' => 'Status',
        ];
    }
}
