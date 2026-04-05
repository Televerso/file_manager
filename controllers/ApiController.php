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

    /**
     * При вызове GET - возвращает все файлы 
     */
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

    /**
     * При вызове POST - создает новый файл
     */
    public function actionCreate()
    {
        $model = new FileForm();
        $model->userID = Yii::$app->user->id;
        $model->user_name = User::findIdentity($model->userID)->user_name;

        $model->uploadFile = UploadedFile::getInstanceByName('file');

        if (!$model->validate()) {
            return ['error' => $model->getFirstError('uploadFile'), 'code' => 400];
        }

        $model->newFileName = Yii::$app->request->post('newFileName');

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

    /**
     * При выхове PUT - обновляет имя файла
     * @param mixed $id - id файла для изменения
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $file = File::findIdentity($id);

        if (!$file) {
            return ['error' => 'Файл не найден', 'code' => 404];
        }

        if ($file->userID != Yii::$app->user->id) {
            return ['error' => 'Нет доступа', 'code' => 403];
        }

        $rawBody = Yii::$app->request->getRawBody();
        $data = json_decode($rawBody, true);
        $newFileName = $data['newFileName'] ?? null;

        // Резервный вариант для form-data
        if (!$newFileName) {
            $newFileName = Yii::$app->request->post('newFileName');
        }

        // Резервный вариант для GET
        if (!$newFileName) {
            $newFileName = Yii::$app->request->get('newFileName');
        }

        if (!$newFileName) {
            return ['error' => 'Не указано newFileName: ' . "'$newFileName'", 'code' => 400];
        }

        $file->file_name = $newFileName;

        if ($file->save()) {
            return ['success' => true, 'file' => $file];
        }

        return ['error' => 'Ошибка обновления', 'code' => 500];
    }

    /**
     * При вызове DELETE - удаляет файл
     * @param mixed $id - id файла для удаления
     */
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
