<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    // имя таблицы в бд
    public static function tableName()
    {
        return 'users';    
    }

    public function rules()
    {
        return [
            [['user_name', 'pass_hash', 'auth_key'], 'required'],
            [['auth_key', 'access_token'], 'unique'],
            [['user_name', 'pass_hash', 'auth_key', 'access_token','salt'], 'string', 'max' => 255],
        ];
    }

    // Поиск по id
    public static function findIdentity($id)
    {
        return static::findOne(['userID'=>$id]);
    }

    // Поиск по токену
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token'=>$token]);
    }

    // Поиск по имени
    public static function findByUserName($name)
    {
        return static::findOne(['user_name'=>$name]);
    }

    public function getId() 
    {
        return $this->getPrimaryKey();    
    }

    public function getAuthKey() 
    {
        return $this->auth_key; 
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password . $this->salt, $this->pass_hash);
    }

    public function setPassword($password)
    {
        $this->salt = Yii::$app->security->generateRandomString();
        $this->pass_hash = Yii::$app->security->generatePasswordHash($password . $this->salt);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function generateAuthKey() 
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }
}
