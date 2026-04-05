<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\FileForm $model */
/** @var app\models\File $files */

use yii\bootstrap5\Html;

$this->title = 'Менеджер файлов';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="app" class="file-manager">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <br>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <!-- Форма загрузки -->
                        <div class="upload-form" >
                            <h4>Загрузить новый файл</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Выберите файл:</label>
                                        <input type="file" @change="handleFileUpload" class="form-control" ref="fileInput">
                                        <small class="text-muted">Максимальный размер: 10MB</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Название файла (опционально):</label>
                                        <input type="text" v-model="newFileName" class="form-control" placeholder="Оставьте пустым для автоматического ввода">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 12.5%; margin-bottom: 0;">
                                        <button @click="uploadFile" :disabled="!selectedFile || uploading" class="btn btn-primary">Загрузить</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Сообщения -->
                            <div v-if="message" class="alert" :class="messageClass" style="margin-top: 15px;">
                                {{ message }}
                            </div>
                            <div v-else class="alert" :class="messageClass" style="margin-top: 15px;">
                                <br>
                            </div>
                        </div>

                        <!-- Таблица файлов -->
                        <div class="files-table">
                            <h4>Список файлов</h4>

                            <div v-if="loading" class="text-center" style="padding: 40px;">
                                Загрузка файлов...
                            </div>

                            <div v-else-if="files.length === 0" class="alert alert-info">
                                У вас пока нет загруженных файлов. Загрузите первый файл с помощью формы выше.
                            </div>

                            <div v-else class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Оригинальное имя</th>
                                            <th>Название</th>
                                            <th>Пользователь</th>
                                            <th>Дата загрузки</th>
                                            <th>Размер</th>
                                            <th style="width: 150px">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="file in files" :key="file.id" :class="{ 'info': editingId === file.id }">
                                            <td>{{ file.id }}</td>
                                            <td>
                                                <span :title="file.file_true_name">
                                                    {{ truncate(file.file_true_name, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span v-if="editingId !== file.id">
                                                    {{ file.file_name }}
                                                </span>
                                                <div v-else>
                                                    <input type="text" v-model="editFileName" class="form-control"
                                                        @keyup.enter="saveEdit(file.id, file.file_name)" @keyup.esc="cancelEdit">
                                                    <small class="text-muted">Enter - сохранить, Esc - отмена</small>
                                                </div>
                                            </td>
                                            <td>{{ file.user_name || 'N/A' }}</td>
                                            <td>{{ formatDate(file.time_modify) }}</td>
                                            <td>{{ file.size || 'N/A' }}</td>
                                            <td>
                                                <div v-if="editingId !== file.id" class="btn-group">
                                                    <a :href="'/file/download?id=' + file.id" class="btn btn-success" title="Скачать">
                                                        Скачать
                                                    </a>
                                                    <button @click="startEdit(file)" class="btn btn-warning" title="Изменить название">
                                                        Редактировать
                                                    </button>
                                                    <button @click="deleteFile(file.id)" class="btn btn-danger" title="Удалить">
                                                        Удалить
                                                    </button>
                                                </div>
                                                <div v-else class="btn-group">
                                                    <button @click="saveEdit(file.id, file.file_name)" class="btn btn-primary" title="Ок">
                                                        Ок
                                                    </button>
                                                    <button @click="cancelEdit" class="btn btn-danger" title="Отмена">
                                                        Отмена
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="/js/file-manager.js"></script>