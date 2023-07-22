<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $model \app\models\SearchForm */

            // Запись времени окончания выполнения скрипта.
            $endTime = microtime(true);
            // Вычисление времени загрузки сайта.
            $executionTimeStart = $endTime - $_SERVER["REQUEST_TIME_FLOAT"];


$startTime = microtime(true);

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm; // Добавьте это
use app\assets\AppAsset;
use app\widgets\BootstrapBreadcrumbs;
use app\components\LoginModalWidget;
use app\components\LoginButtonWidget;
use app\components\PasswordRecoveryFormWidget;
use app\components\RegistrationFormWidget;
use app\components\UserMenuButtonWidget;
use app\components\UserMenuContentWidget;
use app\components\ApiTimer;
use app\components\DbTimer;


AppAsset::register($this);

$model = new \app\models\SearchForm();
$modelUser = new \app\models\User();

$form = ActiveForm::begin([
    'action' => ['/site/search'],
    'method' => 'get',
    'options' => [
        'class' => 'd-flex'
    ]
]);

?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" itemscope itemtype="http://schema.org/WebSite">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>/-->
    <script src="https://connect.ok.ru/connect.js"></script>
</head>
<body>

<?php $this->beginBody() ?>

<div class="wrap">
<?php
    NavBar::begin([
        'brandLabel' => 'Солнышко',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg fixed-top navbar-dark bg-primary  ',
        ],
    ]);
?>
    <div class="collapse navbar-collapse center">
    <?php
    $form = ActiveForm::begin([
        'action' => ['/search'],
        'method' => 'get',
        'options' => [
            'class' => 'd-flex'
        ]
    ]);

    echo $form->field($model, 'q')
        ->textInput(['placeholder' => 'Поиск...', 'class' => 'form-control me-sm-2'])
        ->label(false);

    ActiveForm::end();
    ?>
        </div>
            <?php
            $currentRoute = Yii::$app->controller->getRoute();

            if ($currentRoute != 'pages/login') {
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav ml-auto'],
                    'items' => [
                        Yii::$app->user->isGuest ? (
                        ['label' => 'Войти', 'url' => ['/login']]
                        ) : (
                            '<li class="nav-item">'
                            . UserMenuButtonWidget::widget()
                            . '</li>'
                        )
                    ],
                ]);
            }



    NavBar::end();
    ?>

    <div class="container main-container">
        <?= BootstrapBreadcrumbs::widget([
            'homeLink' => [
                'label' => 'Каталог',
                'url' => '/',
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
        <?= $content ?>
    </div>
</div>
<footer id="footer">
    <div class="container">
    <div class="row">
        <div class="col-lg-12">
            <ul class="list-unstyled">
                <li class="float-end top"><a href="#top">На верх</a></li>
                <li class="float-end"><div id="ok_group_widget" style="margin-left: -30px;"></div></li>
            </ul>
            <p class="pull-left">&copy; Солнышко - коллекция открыток <?= date('Y') ?><br />
                Территория хорошего настроения 😂🤣😂<br /><br />
                <small>Время до рендеринга:
                <?php
                echo number_format($executionTimeStart, 6) . ' сек.';
                ?><br />
                Время рендеринга:
                <?php
                // Запись времени окончания выполнения скрипта.
                $endTime = microtime(true);
                // Вычисление времени загрузки сайта.
                $executionTime = $endTime - $startTime;

                // Вывод времени загрузки с точностью до микросекунд.
                echo number_format($executionTime, 6) . ' сек.';

                ?>
                <?= ApiTimer::getExecutionTimes() ?></small>
                <?= DbTimer::getExecutionTimes() ?>

            </p>
        </div>
    </div>
    </div>
</footer>

<script>
    !function (d, id, did, st) {
        var js = d.createElement("script");
        js.src = "https://connect.ok.ru/connect.js";
        js.onload = js.onreadystatechange = function () {
            if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                if (!this.executed) {
                    this.executed = true;
                    setTimeout(function () {
                        OK.CONNECT.insertGroupWidget(id,did,st);
                    }, 0);
                }
            }};
        d.documentElement.appendChild(js);
    }(document,"ok_group_widget","51957422030974",'{"width":240,"height":105}');
</script>
<?php $this->endBody() ?>
<?php
echo LoginModalWidget::widget(['model' => $modelUser]);
echo PasswordRecoveryFormWidget::widget(['model' => $modelUser]);
echo RegistrationFormWidget::widget(['model' => $modelUser]);
if(!Yii::$app->user->isGuest){
    echo UserMenuContentWidget::widget();
}
?>
</body>
</html>
<?php $this->endPage() ?>
