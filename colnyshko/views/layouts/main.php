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
        'brandLabel' => 'Название вашего сайта',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-dark bg-dark navbar-expand-lg',
        ],
    ]);

    $form = ActiveForm::begin([
        'action' => ['/site/search'],
        'method' => 'get',
        'options' => [
            'class' => 'd-flex'
        ]
    ]);

    echo $form->field($model, 'search')->textInput(['placeholder' => 'Поиск...'])->label(false);

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

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Название вашего сайта <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
