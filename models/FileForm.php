<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class FileForm extends model 
{
    public $userID;
    public $user_name;
    public $newFileName;

    public $uploadFile;

    public function rules()
    {
        return [
            [['uploadFile'], 'required', 'message' => 'Выберите файл для загрузки'],
            [['newFileName'], 'string', 'max' => 64],
            // Для загружаемого файла
            [['uploadFile'], 'file',
                'skipOnEmpty' => true, 
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'pdf', 'doc', 'docx', 'txt'],
                'maxSize' => 20 * 1024 * 1024,  // 20 MB
                'tooBig' => 'Максимальный размер файла 20MB',
                'wrongExtension' => 'Разрешены только: {extensions}',
            ],
        ];
    }

    /**
     * Загрузка файла на сервер и создание соответствующей записи в бд
     */
    public function upload()
    {
        if (!$this->validate()) 
        {
            return false;
        }    

        $fileRecord = new File();
        $fileRecord->userID = $this->userID;
        $fileRecord->user_name = $this->user_name;

        $fileRecord->file_name = empty($this->newFileName) ? $this->uploadFile->name : $this->newFileName;
        $fileRecord->time_modify = date('Y-m-d_H:i:s');
        $fileRecord->file_true_name = $fileRecord->time_modify.'_'.md5($fileRecord->time_modify . $fileRecord->file_name).'_'.$fileRecord->file_name;

        // Путь для сохранения (относительно корня проекта)
        $uploadDir = Yii::getAlias('@webroot') . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileRecord->file_path = 'uploads/' . $fileRecord->file_true_name;

        // Сохраняем файл
        $fullPath = Yii::getAlias('@webroot') . '/' . $fileRecord->file_path;
        
        if (!$fileRecord->save()) {
            Yii::error('Не удалось сохранить запись в БД: ' . json_encode($fileRecord->errors), __METHOD__);
            // Удаляем файл, если не удалось сохранить в БД
            return false;
        }
        if (!$this->uploadFile->saveAs($fullPath)) {
            Yii::error('Не удалось сохранить файл: ' . $fullPath, __METHOD__);
            $fileRecord->delete();
            return false;
        }
        return $fileRecord;
    }
}