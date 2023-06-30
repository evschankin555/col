<?php
namespace app\models;

use yii\base\Model;

class SubCategory extends Model
{
    public $id;
    public $name;
    public $slug;
    public $count;
    public $month;
    public $isActive;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            ['id', 'integer'],
            ['count', 'integer'],
            ['name', 'string'],
            ['slug', 'string'],
            ['month', 'integer'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'count' => 'Count',
            'month' => 'Month',
        ];
    }
}
