<?php
namespace app\models;

use yii\base\Model;

class SearchForm extends Model
{
    public $search;

    public function rules()
    {
        return [
            ['search', 'string'],
        ];
    }
}
