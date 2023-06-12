<?php

namespace app\modules;

use Yii;
use yii\base\Module;
use yii\helpers\Html;
use yii\helpers\Url;
/*
Тег alt для изображений: Помимо указанных мета-тегов, не забывайте про теги alt для всех изображений на вашем сайте, включая логотип. Это помогает поисковым системам понять, что изображено на картинках, и может помочь улучшить ваш SEO.

Структурированные данные: Вы можете рассмотреть возможность использования структурированных данных (схемы), чтобы дать поисковым системам еще больше контекстной информации о вашем сайте. Это может помочь улучшить видимость вашего сайта в результатах поиска.
 * */
class SeoModule extends Module
{
    public $controllerNamespace = 'app\modules\controllers';

    // Добавляем новые свойства
    public $enableOpenGraph = true;
    public $enableTwitter = true;
    public $enableCanonicalUrl = true;

    private $title;
    private $description;
    private $keywords;
    private $author;
    private $siteName;
    private $imageUrl;
    private $pageUrl;
    private $imageWidth;
    private $imageHeight;

    public function init()
    {
        parent::init();
        Yii::$app->language = 'ru-RU';
        // Задаём значения автора и названия сайта
        $this->author = "Евгений";
        $this->siteName = "Солнышко - коллекция открыток";
    }

    public function setImageData($imageUrl, $imageWidth, $imageHeight) {
        $this->imageUrl = $imageUrl;
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;
    }


    public function registerSeoTags()
    {
        $view = \Yii::$app->getView();

        if ($this->title !== null) {
            $view->title = Html::encode($this->title);
        }

        if ($this->description !== null) {
            $view->registerMetaTag([
                'name' => 'description',
                'content' => Html::encode($this->description),
            ], 'description');
        }

        if ($this->keywords !== null) {
            $view->registerMetaTag([
                'name' => 'keywords',
                'content' => Html::encode($this->keywords),
            ], 'keywords');
        }

        if ($this->enableOpenGraph) {
            $this->registerOpenGraphTags($view);
        }

        if ($this->enableTwitter) {
            $this->registerTwitterTags($view);
        }

        if ($this->enableCanonicalUrl) {
            $this->registerCanonicalUrl($view);
        }

    }

    public function setHomePageData($homeData)
    {
        // Здесь устанавливаем title, description и keywords для главной страницы
        $this->title = "Солнышко - Бесплатная коллекция открыток для всех случаев";
        $this->description = "Солнышко предлагает огромную коллекцию бесплатных открыток на каждый день, для пожеланий и любви. Делись красочными открытками в социальных сетях, форумах и мессенджерах, таких как Одноклассники, ВКонтакте, Мой Мир, WhatsApp, Telegram, Viber и других.";
        $this->keywords = "открытки, бесплатные открытки, открытки на каждый день, открытки с пожеланиями, любовные открытки, открытки с цветами, открытки для социальных сетей, открытки для форумов, открытки для мессенджеров";
    }

    public function setCategoryPageData($categoryData)
    {
        // Здесь устанавливаем title, description и keywords для страницы категории
        $this->title = "Категория: " . $categoryData['name'];

        // Создаем различные описания для разных категорий
        switch($categoryData['name']) {
            case 'На каждый день':
                $this->description = "В категории 'На каждый день' Солнышко предлагает бесплатные открытки для поднятия настроения и придания особого шарма вашим сообщениям. Здесь вы найдете уникальную коллекцию открыток, которые помогут вам поделиться своими чувствами и эмоциями.";
                $this->keywords = "открытки на каждый день, повседневные открытки, открытки для повседневных сообщений, ежедневные открытки, бесплатные ежедневные открытки";
                break;
            case 'Пожелания':
                $this->description = "Специально для вас мы подготовили уникальную коллекцию открыток с пожеланиями. Они помогут выразить ваши эмоции и передать искренние пожелания близким и друзьям.";
                $this->keywords = "открытки с пожеланиями, поздравительные открытки, открытки с лучшими пожеланиями, уникальные открытки с пожеланиями";
                break;
            case 'Я тебя люблю':
                $this->description = "Открытки в категории 'Я тебя люблю' - это отличный способ выразить свои чувства. Не бойтесь говорить о любви с помощью наших красивых и эмоциональных открыток.";
                $this->keywords = "любовные открытки, открытки 'Я тебя люблю', открытки с любовью, романтические открытки";
                break;
            case 'Цветы':
                $this->description = "В категории 'Цветы' вы найдете открытки с изображением самых разнообразных и красивых цветов. Подарите своим близким немного красоты и свежести с помощью наших открыток.";
                $this->keywords = "открытки с цветами, цветочные открытки, открытки с букетами, красивые открытки с цветами";
                break;
            default:
                $this->description = "Познакомьтесь с нашей коллекцией открыток в категории: " . $categoryData['name'] . ". Солнышко создает уникальные и красочные открытки для каждого случая и каждого настроения.";
                $this->keywords = $categoryData['name'] . " открытки, бесплатные " . $categoryData['name'] . " открытки, уникальные " . $categoryData['name'] . " открытки";
        }
    }
    public function setSubcategoryPageData($subcategoryData)
    {
        // Здесь устанавливаем title, description и keywords для страницы подкатегории
        $this->title = "Подкатегория: " . $subcategoryData['name'];

        // Создаем различные описания для разных подкатегорий
        switch($subcategoryData['name']) {
            // На каждый день
            case 'Доброе утро':
                $this->description = "Открытки с добрыми утренними пожеланиями помогут начать день с улыбки. Сделайте утро ваших близких еще ярче с помощью нашей коллекции открыток.";
                $this->keywords = "доброе утро открытки, утренние открытки, открытки для утра, пожелания доброго утра";
                break;
            case 'Добрый день':
                $this->description = "Отправьте своим близким пожелания доброго дня с помощью наших открыток. Это маленький жест, который может сделать их день немного ярче.";
                $this->keywords = "добрый день открытки, открытки для дня, пожелания доброго дня";
                break;
            case 'Добрый вечер':
                $this->description = "Отправьте открытку с пожеланием доброго вечера, чтобы сделать вечер ваших близких спокойным и приятным.";
                $this->keywords = "добрый вечер открытки, вечерние открытки, пожелания доброго вечера";
                break;
            case 'Спокойной ночи':
                $this->description = "Открытки со спокойной ночью идеально подходят для завершения дня на позитивной ноте. Отправьте их, чтобы пожелать близким сладких снов.";
                $this->keywords = "спокойной ночи открытки, ночные открытки, пожелания спокойной ночи";
                break;
            case 'Хорошего вечера':
                $this->description = "Открытки с пожеланием хорошего вечера помогут добавить уютности и тепла в конец дня ваших близких. Отправьте их для создания особого вечернего настроения.";
                $this->keywords = "хорошего вечера открытки, вечерние открытки, пожелания хорошего вечера";
                break;
            case 'Хорошего настроения':
                $this->description = "Хотите поднять настроение своим близким? Отправьте им открытку с пожеланием хорошего настроения из нашей коллекции!";
                $this->keywords = "хорошего настроения открытки, открытки для поднятия настроения, пожелания хорошего настроения";
                break;
            case 'Прости меня':
                $this->description = "Если вам нужно извиниться перед кем-то, наши открытки 'Прости меня' помогут вам выразить ваши чувства.";
                $this->keywords = "прости меня открытки, открытки для извинений, открытки с извинениями";
                break;
            case 'Спасибо':
                $this->description = "Выразите свою благодарность и признательность с помощью наших открыток 'Спасибо'. Они помогут вам показать вашу признательность наиболее искренним образом.";
                $this->keywords = "спасибо открытки, открытки для благодарности, открытки с благодарностью";
                break;
                // Пожелания
            case 'Хорошего отдыха':
                $this->description = "Наши открытки с пожеланием хорошего отдыха помогут создать атмосферу релаксации и умиротворения. Отправьте их тем, кто заслуживает немного отдыха!";
                $this->keywords = "хорошего отдыха открытки, открытки для отдыха, пожелания хорошего отдыха";
                break;
            case 'Хороших выходных':
                $this->description = "С нашими открытками с пожеланием хороших выходных вы можете сделать выходные ваших близких немного более особенными!";
                $this->keywords = "хороших выходных открытки, открытки для выходных, пожелания хороших выходных";
                break;
            case 'Улыбнись':
                $this->description = "Открытки с пожеланием улыбнуться способны поднять настроение даже в самый серый день. Дарите радость и улыбки вместе с нами!";
                $this->keywords = "улыбнись открытки, открытки для поднятия настроения, открытки с улыбкой";
                break;
            case 'Красивые пожелания':
                $this->description = "В нашей коллекции 'Красивые пожелания' собраны самые искренние и трогательные слова. Они помогут вам выразить свои чувства и пожелать самого лучшего.";
                $this->keywords = "красивые пожелания открытки, открытки с пожеланиями, открытки с красивыми пожеланиями";
                break;
            case 'Счастья':
                $this->description = "Пожелать счастья - одно из самых добрых и искренних желаний. Сделайте это особенным образом с нашими открытками.";
                $this->keywords = "счастья открытки, открытки с пожеланием счастья, пожелания счастья";
                break;
            case 'Здоровья':
                $this->description = "В нашей подкатегории 'Здоровья' вы найдете открытки, которые помогут вам пожелать близким и друзьям крепкого здоровья и бодрости.";
                $this->keywords = "здоровья открытки, открытки с пожеланием здоровья, пожелания здоровья";
                break;
            case 'Выздоравливай скорее':
                $this->description = "Если кто-то из ваших близких нездоров, наши открытки с пожеланием скорейшего выздоровления помогут поднять их дух и быстрее восстановиться.";
                $this->keywords = "выздоравливай скорее открытки, открытки с пожеланием здоровья, пожелания выздоровления";
                break;
            case 'Береги себя':
                $this->description = "Открытки с пожеланием 'Береги себя' – это ваш способ показать заботу и нежность. Пусть ваши близкие знают, как вы их цените.";
                $this->keywords = "береги себя открытки, открытки с пожеланием заботы, пожелания беречь себя";
                break;
            case 'В дорогу ':
                $this->description = "С нашими открытками 'В дорогу' вы сможете пожелать удачного пути и новых приключений тем, кто отправляется в путешествие.";
                $this->keywords = "в дорогу открытки, открытки для путешественников, пожелания удачного пути";
                break;
            case 'С приездом':
                $this->description = "Ожидание окончено и ваши близкие вернулись домой. Отметьте это событие вместе с нашими открытками 'С приездом'.";
                $this->keywords = "с приездом открытки, открытки для возвращающихся, пожелания с приездом";
                break;
            case 'Приятного аппетита':
                $this->description = "Пожелайте близким насладиться едой с нашими открытками 'Приятного аппетита'. Идеально для совместного обеда или ужина.";
                $this->keywords = "приятного аппетита открытки, открытки для обеда или ужина, пожелания вкусной еды";
                break;
                // Я тебя люблю
            case 'Жду':
                $this->description = "Открытки из нашей коллекции 'Жду' идеально передают ожидание и волнение в преддверии встречи с любимыми.";
                $this->keywords = "жду открытки, открытки с ожиданием, пожелания жду";
                break;
            case 'Скучаю':
                $this->description = "С нашими открытками 'Скучаю' вы можете поделиться своими чувствами с людьми, по которым вы скучаете. Пусть они знают, что вы думаете о них.";
                $this->keywords = "скучаю открытки, открытки с ностальгией, пожелания скучаю";
                break;
            case 'Обнимаю':
                $this->description = "Открытки 'Обнимаю' помогут вам передать тепло и нежность своих объятий, даже когда вы далеко.";
                $this->keywords = "обнимаю открытки, открытки с объятиями, пожелания обнимаю";
                break;
            case 'Целую':
                $this->description = "Покажите свою любовь и страсть с помощью наших открыток 'Целую'. Это идеальный способ сделать день вашего любимого человека особенным.";
                $this->keywords = "целую открытки, открытки с поцелуями, пожелания целую";
                break;
                //Цветы
            case 'Розы':
                $this->description = "Розы символизируют любовь и уважение. Наша коллекция открыток с розами поможет вам передать эти чувства.";
                $this->keywords = "розы открытки, открытки с розами, пожелания с розами";
                break;
            case 'Пионы':
                $this->description = "Пионы - это знак богатства и чести. Поделитесь этими прекрасными цветами с помощью наших открыток.";
                $this->keywords = "пионы открытки, открытки с пионами, пожелания с пионами";
                break;
            case 'Ромашки':
                $this->description = "Ромашки символизируют невинность и чистоту. Наши открытки с ромашками могут помочь вам передать эти добрые пожелания.";
                $this->keywords = "ромашки открытки, открытки с ромашками, пожелания с ромашками";
                break;
            case 'Тюльпаны':
                $this->description = "Тюльпаны обычно ассоциируются с идеальной любовью. Наши открытки с тюльпанами помогут вам выразить свои чувства.";
                $this->keywords = "тюльпаны открытки, открытки с тюльпанами, пожелания с тюльпанами";
                break;
            case 'Лилии':
                $this->description = "Лилии являются символом чистоты и добродетели. Отправьте свои пожелания с помощью наших открыток с лилиями.";
                $this->keywords = "лилии открытки, открытки с лилиями, пожелания с лилиями";
                break;
            case 'Гладиолусы':
                $this->description = "Гладиолусы символизируют силу характера, уважение и почтение. Наши открытки с гладиолусами передадут эти чувства.";
                $this->keywords = "гладиолусы открытки, открытки с гладиолусами, пожелания с гладиолусами";
                break;
            case 'Разное':
                $this->description = "Исследуйте нашу разнообразную коллекцию открыток с разными цветами. Отправьте свое пожелание с нашей уникальной открыткой.";
                $this->keywords = "разные цветы открытки, открытки с разными цветами, пожелания с разными цветами";
                break;
            default:
                $this->description = "Познакомьтесь с нашей коллекцией открыток в подкатегории " . $subcategoryData['name'] . ". Здесь вы найдете открытки для любого случая и настроения.";
                $this->keywords = $subcategoryData['name'] . " открытки, открытки для " . $subcategoryData['name'];
        }
    }

    public function setImagePageData($imageData, $image)
    {
        // Здесь устанавливаем title, description и keywords для страницы с картинкой
        $this->title = $imageData['name'];
        $categoryName = $image['category']['name'];
        if(isset($image['subCategory']) && isset($image['subCategory']['name'])) {
            $categoryName .= ', ' . $image['subCategory']['name'];
        }

        // Обновляем описание, включив в него категорию открытки, более подробное описание самого изображения и возможность отправить открытку в социальные сети
        $this->description = "Открытка " . $imageData['name'] . " из категории " . $categoryName . ". Отправьте уникальную и красочную открытку своим близким и друзьям в Одноклассники, ВКонтакте, WhatsApp, Telegram, Viber и другие социальные сети.";

        // Обновляем ключевые слова, включив в них ключевые слова из категории, связанные с изображением и социальные сети
        $this->keywords = $imageData['name'] . ", открытка, бесплатная открытка, " . $categoryName . ", открытка " . $categoryName . ", Одноклассники, ВКонтакте, WhatsApp, Telegram, Viber, открытка в Одноклассники, открытка в ВКонтакте, " . $this->keywords;
    }



    public function setSearchPageData($searchData)
    {
        // Здесь устанавливаем title, description и keywords для страницы поиска
        $this->title = "Результаты поиска открыток: " . $searchData['query'];

        // Обновляем описание, включив в него информацию о поиске открыток и возможности отправки открыток в социальные сети
        $this->description = "Найдите идеальную открытку на Солнышко. Результаты поиска открыток по запросу '" . $searchData['query'] . "'. Отправьте выбранную открытку в Одноклассники, ВКонтакте, WhatsApp, Telegram, Viber и другие социальные сети.";

        // Обновляем ключевые слова, включив в них поисковый запрос, типы открыток и социальные сети
        $this->keywords = $searchData['query'] . ", поиск открыток, бесплатные открытки, Одноклассники, ВКонтакте, WhatsApp, Telegram, Viber, открытка в Одноклассники, открытка в ВКонтакте, " . $this->keywords;
    }


    private function registerOpenGraphTags($view)
    {
        $view->registerMetaTag(['property' => 'og:title', 'content' => $this->title], 'og:title');
        $view->registerMetaTag(['property' => 'og:site_name', 'content' => $this->siteName], 'og:site_name');
        $view->registerMetaTag(['property' => 'og:url', 'content' => Yii::$app->params['baseUrl'] . $this->pageUrl], 'og:url');
        $view->registerMetaTag(['property' => 'og:description', 'content' => $this->description], 'og:description');
        $view->registerMetaTag(['property' => 'og:image', 'content' => Yii::$app->params['baseUrl'] . $this->imageUrl], 'og:image');
        $view->registerMetaTag(['property' => 'og:locale', 'content' => str_replace('-', '_', Yii::$app->language)], 'og:locale');
        $view->registerMetaTag(['property' => 'og:type', 'content' => 'website'], 'og:type');
        $view->registerMetaTag(['property' => 'og:image:width', 'content' => $this->imageWidth], 'og:image:width');
        $view->registerMetaTag(['property' => 'og:image:height', 'content' => $this->imageHeight], 'og:image:height');
    }

        /*
         Open Graph теги: Не забывайте, что для og:url и og:image вам необходимо указывать абсолютные URL,
        то есть полный путь к ресурсу, включая https://. Кроме того, размер изображения для Open Graph тегов обычно должен быть больше,
        чем 180x180 пикселей. Рекомендуемый размер - 1200x630 пикселей для лучшего отображения в большинстве социальных сетей.
        Если у вас есть возможность, лучше создать отдельное изображение для Open Graph тегов с рекомендуемыми размерами.
         */

    private function registerTwitterTags($view)
    {
        $view->registerMetaTag(['name' => 'twitter:url', 'content' => $this->pageUrl], 'twitter:url');
        $view->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title], 'twitter:title');
        $view->registerMetaTag(['name' => 'twitter:description', 'content' => $this->description], 'twitter:description');
        $view->registerMetaTag(['name' => 'twitter:site', 'content' => "@" . $this->author], 'twitter:site');
        $view->registerMetaTag(['name' => 'twitter:creator', 'content' => "@" . $this->author], 'twitter:creator');
        $view->registerMetaTag(['name' => 'twitter:image', 'content' => $this->imageUrl], 'twitter:image');
        $view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'], 'twitter:card');
    }

    private function registerCanonicalUrl($view)
    {
        $canonicalUrl = Yii::$app->params['baseUrl'] . $this->pageUrl;
        $view->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl], 'canonical');
    }

    public function registerJsonLdScript($data)
    {
        /*
         $seoModule = new SeoModule;
$seoModule->registerJsonLdScript([
    "@context" => "http://schema.org",
    "@type" => "Product",
    "name" => "Хорошего настроения!",
    "image" => "https://legkie-otkrytki.ru/images/147/1ad1856fbe7feada121b5ba0e7d3a1b8/tran.mp4",
    "description" => "Карточка 'Хорошего настроения!' для отправки в социальных сетях.",
    "url" => "https://yourwebsite.com/na-kazhdyy-den/horoshego-nastroeniya/Horoshego-nastroeniya-card-1ad1856fbe7feada121b5ba0e7d3a1b8",
    "category" => "На каждый день"
]);

         */
        $view = \Yii::$app->getView();

        // Проверяем, что данные переданы в виде массива
        if (!is_array($data)) {
            throw new \Exception("Data for JSON-LD script must be an array.");
        }

        // Кодируем данные в формате JSON
        $json = json_encode($data);

        // Регистрируем скрипт в представлении
        $view->registerJs(<<<JS
        var script = document.createElement('script');
        script.type = "application/ld+json";
        script.text = JSON.stringify($json);
        document.getElementsByTagName('head')[0].appendChild(script);
    JS, $view::POS_END);
    }

}