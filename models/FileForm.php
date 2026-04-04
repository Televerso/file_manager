<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class FileForm extends model 
{
    public $userID;
    public $user_name;

    public $uploadFile;

    public function rules()
    {
        return [
            [['uploadFile'], 'required', 'message' => 'Выберите файл для загрузки'],
            // Для загружаемого файла
            [['uploadFile'], 'file',
                'skipOnEmpty' => true, 
                'extensions' => ['jpg', 'png', 'pdf', 'doc', 'docx', 'txt'],
                'wrongExtension' => 'Разрешены только: {extensions}',
                'maxSize' => 10 * 1024 * 1024,  // 10 MB
                'tooBig' => 'Максимальный размер файла 10MB',
            ],
        ];
    }

    public function upload()
    {
        if (!$this->validate()) 
        {
            return false;
        }    

        $fileRecord = new File();

        $fileRecord->file_name = $this->uploadFile->name;
        $fileRecord->userID = $this->userID;
        $fileRecord->user_name = $this->user_name;
        $fileRecord->time_modify = date('Y-m-d_H:i:s');
        $fileRecord->real_file_name = $fileRecord->time_modify.'_'.md5($fileRecord->time_modify . $fileRecord->file_name).'_'.$fileRecord->file_name;

        // Путь для сохранения (относительно корня проекта)
        $uploadDir = Yii::getAlias('@webroot') . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileRecord->file_path = 'uploads/' . $fileRecord->real_file_name;

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