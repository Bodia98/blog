<?php


namespace app\models;


use yii\base\Model;

class SignupForm extends Model
{
    public $name;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            ['password', 'string', 'min' => 6],
            [['name'], 'string'],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => 'app\models\User', 'targetAttribute' => 'email'],
        ];
    }

    public function signup()
    {
        if($this->validate())
        {
            $user = new User();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->setPassword($this->password);

            return $user->create();
        }
    }
}