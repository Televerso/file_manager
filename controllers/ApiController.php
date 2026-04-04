<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\File;
use app\models\FileForm;
use app\models\User;

class ApiController extends Controller
{
    // Отключаем CSRF для API
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    public function actionFiles()
    {
        $userID = Yii::$app->user->id;

        $files = File::findByUserID($userID);

        $result = [];

        foreach ($files as $file) {
            $result[] = [
                'id' => $file->fileID,
                'file_true_name' => $file->file_true_name,
                'file_name' => $file->file_name,
                'file_path' => $file->file_path,
                'user_id' => $file->userID,
                'user_name' => $file->user_name,
                'time_modify' => $file->time_modify,
                'size' => $file->getFormattedSize(),
            ];
        }

        return $result;
    }

    public function actionCreate()
    {
        $model = new FileForm();
        $model->userID = Yii::$app->user->id;
        $model->user_name = User::findIdentity($model->userID)->user_name;

        $model->uploadFile = UploadedFile::getInstanceByName('file');

        $model->newFileName = Yii::$app->request->post('newFileName');

        if (!$model->uploadFile) {
            return ['error' => 'Файл не загружен', 'code' => 400];
        }
        $fileRecord = $model->upload();
        if (!$fileRecord) {
            return ['error' => 'Ошибка сохранения файла', 'code' => 500]; 
        }
        return [
                'success' => true,
                'file' => [
                    'id' => $fileRecord->fileID,
                    'file_true_name' => $fileRecord->file_true_name,
                    'file_name' => $fileRecord->file_name,
                    'file_path' => $fileRecord->file_path,
                    'user_id' => $fileRecord->userID,
                    'user_name' => $fileRecord->user_name,
                    'time_modify' => $fileRecord->time_modify,
                    'size' => $fileRecord->getFormattedSize(),
                ]
        ];
    }

    public function actionUpdate($id) 
    {
        $file = File::findIdentity($id);

        if (!$file) {
            return ['error' => 'Файл не найден', 'code' => 404];
        }

        if ($file->userID != Yii::$app->user->id) {
            return ['error' => 'Нет доступа', 'code' => 403];
        }

        $newFileName = Yii::$app->request->getBodyParam('file_name');
        if (!$newFileName) {
            return ['error' => 'Не указано file_name', 'code' => 400];
        }

        $file->file_name = $newFileName;

        if ($file->save()) {
            return ['success' => true, 'file' => $file];
        }

        return ['error' => 'Ошибка обновления', 'code' => 500];
    }

    public function actionDelete($id)
    {
        $file = File::findIdentity($id);
        
        if (!$file) {
            return ['error' => 'Файл не найден', 'code' => 404];
        }
        
        // Проверка прав
        if ($file->userID != Yii::$app->user->id) {
            return ['error' => 'Нет доступа', 'code' => 403];
        }

        // Удаляем запись из БД
        if ($file->delete()) {
            return ['success' => true];
        }
        
        return ['error' => 'Ошибка удаления', 'code' => 500];
    }
}