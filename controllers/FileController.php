<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\models\File;
use app\models\FileForm;
use app\models\User;

class FileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],  // Только для авторизованных пользователей
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function actionVueIndex()
    {
        return $this->render('vue-index');
    }

    // public function actionIndex()
    // {
    //     $userID = Yii::$app->user->id;
    //     $user_name = User::findIdentity($userID)->user_name;

    //     $model = new FileForm();
    //     $model->userID = $userID;
    //     $model->user_name = $user_name;

    //     return $this->render('index', [
    //         'files' => File::listAll(),
    //         'model' => $model,
    //     ]);
    // }

    // public function actionUpload()
    // {
    //     $model = new FileForm();
    //     $model->userID = Yii::$app->user->id;
    //     $model->user_name = User::findIdentity($model->userID)->user_name;

    //     if (Yii::$app->request->isPost) {
    //         $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
    //         $model->newFileName = Yii::$app->request->post('FileForm')['newFileName'];
    
    //         $savedFileRecord = $model->upload();
    //         if ($savedFileRecord === null) {
    //             Yii::$app->session->setFlash('error', 'Ошибка загрузки файла');
    //         } 
    //         else {
    //             Yii::$app->session->setFlash('success', 'Файл "' . $savedFileRecord->file_name . '" успешно загружен');
    //             return $this->redirect(['index']);
    //         }
    //     }

    //     return $this->render('upload', ['model' => $model]);
    // }

    public function actionDownload($id)
    {
        $file = File::findIdentity($id);

        $fullPath = Yii::getAlias('@webroot') . '/' . $file->file_path;

        if (file_exists($fullPath)) {
            return Yii::$app->response->sendFile($fullPath, $file->file_name);
        }
        else {
            Yii::$app->session->setFlash('error', 'Файл не найден на сервере');
        }
    }

    // public function actionDelete($id)
    // {
    //     $file = File::findIdentity($id);
    //     $file_name = $file->filename;
        
    //     if ($file->delete()) {
    //         Yii::$app->session->setFlash('success', 'Файл "' . $file_name . '" удален');
    //     } 
    //     else {
    //         Yii::$app->session->setFlash('error', 'Ошибка удаления файла');
    //     } 
    //     return $this->redirect(['index']);
    // }
}
