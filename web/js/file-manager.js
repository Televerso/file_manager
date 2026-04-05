const FileManager = {
    data() {
        return {
            files: [],
            loading: true,
            uploading: false,
            selectedFile: null,
            newFileName: '',
            message: '',
            messageClass: '',
            editingId: null,
            editFileName: '',
        }
    },
    mounted() {
        this.loadFiles();
    },
    methods: {
        // Загрузка списка файлов
        loadFiles() {
            this.loading = true;
            fetch('/api/files')
                .then(response => response.json())
                .then(data => {
                    this.files = data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    this.showMessage('Ошибка загрузки списка файлов', 'alert-danger');
                    this.loading = false;
                });
        },

        // Выбор файла
        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
        },

        // Загрузка файла
        uploadFile() {
            if (!this.selectedFile) {
                this.showMessage('Выберите файл для загрузки', 'alert-warning');
                return;
            }

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            if (this.newFileName) {
                formData.append('newFileName', this.newFileName);
            }

            this.uploading = true;

            fetch('/api/files', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showMessage('Файл "' + (this.newFileName || this.selectedFile.name) + '" успешно загружен', 'alert-success');
                        this.loadFiles(); // Обновляем список
                        this.resetUploadForm();
                    } else {
                        this.showMessage(data.error || 'Ошибка загрузки файла', 'alert-danger');
                    }
                    this.uploading = false;
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    this.showMessage('Ошибка загрузки файла', 'alert-danger');
                    this.uploading = false;
                });
        },

        // Сброс формы загрузки
        resetUploadForm() {
            this.selectedFile = null;
            this.newFileName = '';
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        // Начало редактирования
        startEdit(file) {
            this.editingId = file.id;
            this.editFileName = file.file_name;
        },

        // Сохранение изменений
        saveEdit(id, file_name) {
            if (!this.editFileName.trim()) {
                this.showMessage('Название файла не может быть пустым', 'alert-warning');
                return;
            }
            if (this.editFileName === file_name) {
                this.showMessage('Название файла не изменено', 'alert-warning');
                this.cancelEdit();
            }
            else {
                fetch('/api/files/' + id, {
                    method: 'PUT',
                    body: JSON.stringify({
                        newFileName: this.editFileName
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showMessage('Название файла обновлено', 'alert-success');
                            this.loadFiles();
                            this.cancelEdit();
                        } else {
                            this.showMessage(data.error || 'Ошибка обновления', 'alert-danger');
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        this.showMessage('Ошибка обновления названия файла', 'alert-danger');
                    });
            }
        },

        // Отмена редактирования
        cancelEdit() {
            this.editingId = null;
            this.editFileName = '';
        },

        // Удаление файла
        deleteFile(id) {
            if (!confirm('Вы уверены, что хотите удалить этот файл? Это действие нельзя отменить.')) {
                return;
            }

            fetch('/api/files/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showMessage('Файл успешно удален', 'alert-success');
                        this.loadFiles(); // Обновляем список
                    } else {
                        this.showMessage(data.error || 'Ошибка удаления файла', 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    this.showMessage('Ошибка удаления файла', 'alert-danger');
                });
        },

        // Показать сообщение
        showMessage(msg, type) {
            this.message = msg;
            this.messageClass = type;
            setTimeout(() => {
                this.message = '';
                this.messageClass = '';
            }, 5000);
        },

        // Форматирование даты
        formatDate(date) {
            if (!date) return 'Н/Д';
            try {
                return new Date(date).toLocaleString('ru-RU');
            } catch (e) {
                return date;
            }
        },

        // Обрезка длинных строк
        truncate(str, length) {
            if (!str) return '';
            if (str.length <= length) return str;
            return str.substr(0, length) + '...';
        }
    }
};

document.addEventListener('DOMContentLoaded', ()=>{
    new Vue({
        el: '#app',
        ...FileManager
    });
});
