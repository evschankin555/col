<?php
    namespace app\models\user_related;

    use Yii;
    use app\components\TimedActiveRecord;
    use app\models\Upload;

    class Image extends TimedActiveRecord
    {
        public static function tableName()
        {
            return 'images';
        }

        public function rules()
        {
            return [
                [['url', 'user_id'], 'required'],
                [['url', 'short_url'], 'string', 'max' => 2048],
                [['description'], 'string'],
                [['upload_id', 'user_id'], 'integer'],
            ];
        }

        public function attributeLabels()
        {
            return [
                'id' => 'ID',
                'url' => 'Image URL',
                'description' => 'Description',
                'upload_id' => 'Upload ID',
                'user_id' => 'User ID',
                'short_url' => 'Short URL',
            ];
        }

        public function getUpload()
        {
            return $this->hasOne(Upload::class, ['id' => 'upload_id']);
        }

        public static function createNew($imageUrl, $description = null, $userId = null) {
            $image = new self();
            $image->url = $imageUrl;
            $image->description = $description;
            $image->user_id = $userId;

            // Извлекаем короткий URL
            $image->short_url = basename($imageUrl);

            // Извлекаем запись Upload по cloud_url
            $upload = Upload::findOne(['cloud_url' => $imageUrl]);
            if ($upload) {
                $image->upload_id = $upload->id;
            }

            return $image->save() ? $image : null;
        }
    }
