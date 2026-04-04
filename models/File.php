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

    public function rules()
    {
        return [
            [['file_true_name', 'file_name', 'file_path'], 'required'],
            [['file_true_name', 'file_name', 'file_path', 'user_name'], 'string', 'max' => 255],
            [['userID'], 'integer'],
            [['time_modify'], 'safe'],
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['fileID'=>$id]);
    }

    // Связка с user
    public function getUserByID()
    {
        return $this->hasOne(User::class, ['userID' => 'userID', 'user_name'=>'user_name']);
    }

    // public function upload()
    // {
    //     if ($this->validate() && $this->uploadFile) {
    //         $this->file_name = $this->uploadFile->name;
    //         $this->time_modify = date('Y-m-d H:i:s');
    //         $this->real_file_name = $this->time_modify . md5($this->time_modify . $this->file_name) . $this->file_name;

    //         // Путь для сохранения (относительно корня проекта)
    //         $uploadDir = Yii::getAlias('@webroot') . '/uploads/';
    //         if (!is_dir($uploadDir)) {
    //             mkdir($uploadDir, 0755, true);
    //         }

    //         $this->file_path = 'uploads/' . $this->file_name;
    //         $fullPath = Yii::getAlias('@webroot') . '/' . $this->file_path;
    //         // Сохраняем файл
    //         if ($this->uploadFile->saveAs($fullPath)) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }

    public function getFileUrl()
    {
        return Yii::$app->request->baseUrl . '/' . $this->file_path;
    }

    public function deleteFile()
    {
        $fullPath = Yii::getAlias('@webroot') . '/' . $this->file_path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->deleteFile();
            return true;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->time_modify = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public static function findByUserID($userID)
    {
        return static::find()->where(['userID' => $userID])->orderBy(['time_modify' => SORT_DESC]);
    }

    public static function findByUserName($user_name)
    {
        return static::find()->where(['user_name' => $user_name])->orderBy(['time_modify' => SORT_DESC]);
    }

    public static function listAll()
    {
        return static::find()->all();
    }

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
