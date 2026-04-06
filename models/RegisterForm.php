<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the registration form.
 *
 * @property-read User|null $user
 *
 */
class RegisterForm extends Model
{
    public $user_name;
    public $password;
    public $password_repeat;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['user_name', 'password', 'password_repeat'], 'required'],
            ['user_name', 'string', 'min' => 3, 'max' => 64],
            ['password', 'string', 'min' => 6],
            // password is validated by validatePassword()
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают!'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_name' => 'Логин',  // ← было 'User Name' или 'user_name'
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
        ];
    }


    /**
     * Регистрирует пользователя с указанными user_name и password
     * @return bool - был ли пользователь успешно зарегистрирован
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->user_name = $this->user_name;

        $user->setPassword($this->password);
        $user->generateAuthKey();

        // Если есть поле access_token для API
        $user->generateAccessToken();


        return $user->save() ? $user : false;
    }
}
