<?php
    namespace app\models\user_related;

    use Yii;
    use app\components\TimedActiveRecord;
    use app\models\Upload;
    use app\models\User;

    class Image extends TimedActiveRecord
    {

        public $href;
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

        public function getUser()
        {
            return $this->hasOne(User::class, ['id' => 'user_id']);
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

        public function generateHref()
        {
            $username = $this->user->username;  // Получаем имя пользователя
            $titleTranslit = $this->translit($this->description);  // Получаем транслитерированный title
            $idEncoded = dechex($this->id);  // Кодируем ID в base64 (или в 16-ричную систему счисления)

            return "http://localhost/{$username}/{$titleTranslit}-{$idEncoded}";
        }

        private function translit($string)
        {
            $converter = array(
                'а' => 'a',   'б' => 'b',   'в' => 'v',
                'г' => 'g',   'д' => 'd',   'е' => 'e',
                'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
                'и' => 'i',   'й' => 'y',   'к' => 'k',
                'л' => 'l',   'м' => 'm',   'н' => 'n',
                'о' => 'o',   'п' => 'p',   'р' => 'r',
                'с' => 's',   'т' => 't',   'у' => 'u',
                'ф' => 'f',   'х' => 'h',   'ц' => 'c',
                'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
                'ь' => '',    'ы' => 'y',   'ъ' => '',
                'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

                'А' => 'a',   'Б' => 'b',   'В' => 'v',
                'Г' => 'g',   'Д' => 'd',   'Е' => 'e',
                'Ё' => 'e',   'Ж' => 'zh',  'З' => 'z',
                'И' => 'i',   'Й' => 'y',   'К' => 'k',
                'Л' => 'l',   'М' => 'm',   'Н' => 'n',
                'О' => 'o',   'П' => 'p',   'Р' => 'r',
                'С' => 's',   'Т' => 't',   'У' => 'u',
                'Ф' => 'f',   'Х' => 'h',   'Ц' => 'c',
                'Ч' => 'ch',  'Ш' => 'sh',  'Щ' => 'sch',
                'Ь' => '',    'Ы' => 'y',   'Ъ' => '',
                'Э' => 'e',   'Ю' => 'yu',  'Я' => 'ya',
            );

            $string = strtr($string, $converter); // Транслитерация
            $string = strtolower($string); // Преобразование в нижний регистр
            $string = preg_replace('/\s+/', '-', $string); // Замена пробелов на тире
            $string = preg_replace('/[^a-zA-Z0-9\-]/', '', $string); // Удаление всего, что не буква или цифра или тире
            $string = preg_replace('/-+/', '-', $string); // Замена повторяющихся тире на один
            return $string;
        }

        public function afterFind()
        {
            parent::afterFind();
            $this->href = $this->generateHref();
        }

    }
