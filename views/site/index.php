<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'Менеджер файлов на Yii2+Vue2';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="body-content">
        <div class="jumbotron text-center bg-transparent mt-5 mb-5">
            <h4 class="display-8">Тут хранятся ваши файлы</h4>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <h2>Функционал:</h2>
                <p></p>
                <p>- Авторизация пользователей</p>
                <p>- Таблица со списком файлов</p>
                <p>- Загрузка файлов</p>
                <p>- Редактирование имени файла</p>
                <p>- Удаление и скачивание файлов</p>
            </div>
            <div class="col-lg-4 col-md-6">
                <h2>Используемый стек: </h2>
                <p></p>
                <p>- docker</p>
                <p>- docker-compose</p>
                <p>- php 7.4</p> 
                <p>- yii2-basic</p> 
                <p>- nginx 1.20.1</p>
                <p>- mysql 5.7.41</p> 
                <p>- composer</p>
                <p>- gii</p> 
                <p>- vue2</p>
            </div>
            <div class="col-lg-4 col-md-6">
                <h2>API:</h2>
                <p></p>
                <p>- `GET /api/files` - список файлов</p>
                <p>- `POST /api/files` - загрузка файла</p>
                <p>- `PUT /api/files/{id}` - изменение имени</p>
                <p>- `DELETE /api/files/{id}` - удаление</p>
            </div>
        </div>

    </div>
</div>
