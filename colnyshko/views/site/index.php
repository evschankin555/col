<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Nav;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Button;
use GuzzleHttp\Client;
use yii\caching\FileCache;

$this->title = 'Солнышко';
?>

    <div class="jumbotron">
            <?php
            echo '<p class="lead main-categories">';
            foreach ($categories as $category) {
                echo Html::a(
                    $category->name . '<span class="badge bg-secondary">' . $category->count . '</span>',
                    $category->id == 0 ? '/' : ['site/category', 'slug' => $category->slug],
                    [
                        'class' => $category->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm',
                        'title' => $category->name . ' (' . $category->count . ')',
                    ]
                );
            }
            echo '</p>';

            foreach ($categories as $category) {
                if ($category->isActive) {
                    echo '<p class="lead sub-categories">';
                    foreach ($category->subCategories as $subCategory) {
                        echo Html::a(
                            $subCategory->name. '<span class="badge bg-secondary">' . $subCategory->count . '</span>',
                            ['site/sub-category', 'slug' => $subCategory->slug],
                            [
                                'class' => $subCategory->isActive ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm',
                                'title' => $subCategory->name . ' (' . $subCategory->count . ')',
                            ]
                        );
                    }
                    echo '</p>';
                }
            }


            ?>
        <hr class="my-2">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 1</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 2</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Плитка 3</h5>
                    <p class="card-text">Какое-то описание.</p>
                </div>
            </div>
        </div>
    </div>
