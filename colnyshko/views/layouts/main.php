<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $model \app\models\SearchForm */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm; // Добавьте это
use app\assets\AppAsset;

AppAsset::register($this);

$model = new \app\models\SearchForm();

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
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<div class="wrap">
<?php
    NavBar::begin([
        'brandLabel' => 'Солнышко - коллекция открыток',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg fixed-top navbar-dark bg-primary',
        ],
    ]);
?>
    <div class="collapse navbar-collapse center">
    <?php

    $form = ActiveForm::begin([
        'action' => ['/site/search'],
        'method' => 'get',
        'options' => [
            'class' => 'd-flex'
        ]
    ]);

   echo $form->field($model, 'search')
       ->textInput(['placeholder' => 'Поиск...', 'class' => 'form-control me-sm-2'])
       ->label(false);


    ActiveForm::end();
    ?>
        </div>
            <?php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ml-auto'],
        'items' => [
            Yii::$app->user->isGuest ? (
            ['label' => 'Войти', 'url' => ['/site/login']]
            ) : (
                '<li class="nav-item">'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link nav-link']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= $content ?>
    </div>
</div>
<footer id="footer">
    <div class="container">
    <div class="row">
        <div class="col-lg-12">
            <ul class="list-unstyled">
                <li class="float-end"><a href="#top">На верх</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">RSS</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">API</a></li>
                <li class="float-end"><div id="ok_group_widget" style="margin-left: -30px;"></div></li>
            </ul>
            <p class="pull-left">&copy; Солнышко - коллекция открыток <?= date('Y') ?></p>
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/js/bootstrap.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
