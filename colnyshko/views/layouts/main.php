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
        'brandLabel' => 'Солнышко',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg fixed-top navbar-dark bg-primary',
        ],
    ]);

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
            </ul>
            <p class="pull-left">&copy; Солнышко <?= date('Y') ?></p>
        </div>
    </div>
    </div>
</footer>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
