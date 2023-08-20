<?php
    namespace app\models\user_related;

    use Yii;
    use app\components\TimedActiveRecord;

    class Image extends TimedActiveRecord
    {
        public static function tableName()
        {
            return 'images';
        }

        public function rules()
        {
            return [
                [['url'], 'required'],
                [['url', 'short_url'], 'string', 'max' => 2048],
                [['description'], 'string'],
                [['upload_id'], 'integer'],
            ];
        }

        public function attributeLabels()
        {
            return [
                'id' => 'ID',
                'url' => 'Image URL',
                'description' => 'Description',
                'upload_id' => 'Upload ID',
                'short_url' => 'Short URL',
            ];
        }

        public function getUpload()
        {
            return $this->hasOne(Upload::class, ['id' => 'upload_id']);
        }
    }
