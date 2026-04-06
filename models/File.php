<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class File extends ActiveRecord
{
    public $uploadFile;

    // имя таблицы в бд
    public static function tableName()
    {
        return 'files';    
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_true_name', 'file_name', 'file_path'], 'required'],
            [['file_true_name', 'file_name', 'file_path', 'user_name'], 'string', 'max' => 255],
            [['userID'], 'integer'],
            [['time_modify'], 'safe'],
        ];
    }

    /**
     * @param int $id - поиск файла по id
     * @return static | null - ActiveRecord, совпадающий с условием
     */
    public static function findIdentity($id)
    {
        return static::findOne(['fileID'=>$id]);
    }

    // Связка с user
    public function getUser()
    {
        return $this->hasOne(User::class, ['userID' => 'userID', 'user_name'=>'user_name']);
    }

    public function getFileUrl()
    {
        return Yii::$app->request->baseUrl . '/' . $this->file_path;
    }

    /**
     * Удаляет файл, соответствующей данной записи
     */
    public function deleteFile()
    {
        $fullPath = Yii::getAlias('@webroot') . '/' . $this->file_path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->deleteFile();
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->time_modify = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Поиск всех файлов пользователя
     * @param int $userID - id пользователя
     */
    public static function findByUserID($userID)
    {
        return static::find()->where(['userID' => $userID])->orderBy(['time_modify' => SORT_DESC])->all();
    }

    /**
     * Поиск всех файлов пользователя
     * @param String $user_name - имя пользователя
     */
    public static function findByUserName($user_name)
    {
        return static::find()->where(['user_name' => $user_name])->orderBy(['time_modify' => SORT_DESC])->all();
    }

    /**
     * Возвращает все имеющиеся файлы
     */
    public static function listAll()
    {
        return static::find()->all();
    }

    /**
     * Форматирует размер файла в байтах в строку
     */
    public function getFormattedSize()
    {
        $path = Yii::getAlias('@webroot') . '/' . $this->file_path;
        if (file_exists($path)) {
            $bytes = filesize($path);
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return '0 B';
    }
}
