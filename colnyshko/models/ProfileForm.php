<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $username;
    public $avatar;
    public $social_links;
    public $websites;
    public $social_integration;
    public $description;
    public $display_name;
    public $new_password;
    public $confirm_password;

    private $_user;

    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['display_name', 'required'],
            ['display_name', 'string', 'max' => 255],
            ['avatar', 'string', 'max' => 255],
            ['social_links', 'safe'],
            ['websites', 'safe'],
            ['social_integration', 'safe'],
            ['description', 'safe'],
            ['new_password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    public function loadCurrent($user)
    {
        $this->_user = $user;
        $this->username = $user->username;
        $this->avatar = $user->avatar;
        $this->social_links = $user->social_links;
        $this->websites = $user->websites;
        $this->social_integration = $user->social_integration;
        $this->description = $user->description;
        $this->display_name = $user->display_name;
    }

    public function updateProfile()
    {
        if ($this->validate()) {
            $this->_user->username = $this->username;
            $this->_user->avatar = $this->avatar;
            $this->_user->social_links = $this->social_links;
            $this->_user->websites = $this->websites;
            $this->_user->social_integration = $this->social_integration;
            $this->_user->description = $this->description;
            $this->_user->display_name = $this->display_name;
            if (!empty($this->new_password)) {
                $this->_user->password = Yii::$app->security->generatePasswordHash($this->new_password);
            }
            return $this->_user->save();
        }
        return false;
    }
    public function getAvatarUrl()
    {
        // Вернуть результат вызова getAvatarUrl() из экземпляра _user
        return $this->_user->getAvatarUrl();
    }

}
