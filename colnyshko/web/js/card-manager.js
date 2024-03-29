class CardManager {
    /**
     * Конструктор класса CardManager.
     * Инициализирует объект и вызывает метод init для привязки событий.
     */
    constructor() {
        this.init();
    }

    /**
     * Инициализация класса.
     * Вызывает метод bindEvents для привязки всех необходимых событий.
     */
    init() {
        this.errorClasses = ['list-group-item-primary', 'list-group-item-secondary'];
        this.currentErrorClassIndex = 0;
        this.hasErrors = false;
        this.isCanceled = false;
        this.isSavePostCard = false;
        this.isMovePostCard = false;
        this.isDeletePostCard = false;
        this.collectionId = 0;
        this.categoryId = 0;
        this.gtfsUpload = $('#gtfs_upload');
        this.fileUploadContainer = $('.file-upload-container');
        /*this.initializeCounters();*/
        this.initializeMasonry();
        this.bindEvents();
    }

    /**
     * Привязка событий к элементам страницы.
     * Устанавливает обработчики событий для операций с почтовыми карточками.
     */
    bindEvents() {
        $('#addPostcard').on('shown.bs.modal', this.setDropdownWidth.bind(this));
        $('#addPostcardButton').click(this.showAddPostcardModal.bind(this));
        $('#save-postcard-btn').click(this.savePostcard.bind(this));
        this.fileUploadContainer.on('dragover', this.preventDefaultActions.bind(this));
        this.fileUploadContainer.on('dragenter', this.preventDefaultActions.bind(this));
        this.fileUploadContainer.on('drop', this.handleFileDrop.bind(this));
        $('#browse-link').click(this.triggerFileUpload.bind(this));
        this.gtfsUpload.on('change', this.handleFileChange.bind(this));
        $('#del_file_upload').on('click', this.handleDeleteLocalFile.bind(this));
        $('#del_file_upload-image').on('click', this.handleDeleteFromCloud.bind(this));
        $('#closeModalButtonAddPostcard').on('click', this.handleCloseModal.bind(this));
        $(document).on('click', '.category-buttons .dropdown-item:not(#createCategoryButtonAddCard), .collection-buttons .dropdown-item:not(#createCollectionButtonAddCard)', this.handleDropdownItemClick.bind(this));
        $(document).on('click', '#createCollectionButtonAddCard', this.showCollectionCreationForm.bind(this));
        $(document).on('click', '#createCategoryButtonAddCard', this.showCategoryCreationForm.bind(this));
        $(document).on('click', '#createCollectionButtonForm', this.createAndAddCollectionHandler.bind(this));
        $(document).on('click', '#createCategoryButtonForm', this.createAndAddCategoryHandler.bind(this));
        $(document).on('click', '#cancelCollectionButton', this.hideCollectionCreationForm.bind(this));
        $(document).on('click', '#cancelCategoryButton', this.hideCategoryCreationForm.bind(this));
        $('.user-images .card').on('click', this.toggleActiveCard.bind(this));
        document.querySelectorAll('.js-card').forEach(card => {
            card.addEventListener('mouseover', this.handleMouseOverCard.bind(this));
            card.addEventListener('mouseout', this.handleMouseOutCard.bind(this));
        });
        document.querySelectorAll('.save-button').forEach(saveCard => {
            saveCard.addEventListener('click', this.showSavePostcardModal.bind(this));
        });
        document.querySelectorAll('.move-button').forEach(saveCard => {
            saveCard.addEventListener('click', this.showMovePostcardModal.bind(this));
        });
        document.querySelectorAll('.del-button').forEach(delCard => {
            delCard.addEventListener('click', this.showDeletePostcardModal.bind(this));
        });
    }

    /**
     * Установка ширины выпадающего меню.
     * Устанавливает ширину выпадающего меню равной ширине кнопки.
     */
    setDropdownWidth() {
        const buttons = document.querySelectorAll('.collection-buttons, .category-buttons');
        buttons.forEach(btn => {
            const width = btn.offsetWidth;
            const dropdownMenu = btn.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.style.width = `${width}px`;
            }
        });
    }
    /**
     * Показать модальное окно для добавления почтовой карточки.
     * Инициализирует и отображает модальное окно, а также загружает коллекции и категории.
     */
    showAddPostcardModal() {
        this.isCanceled = false;
        this.isMovePostCard = false;
        this.isSavePostCard = false;
        this.initializeFormPostcardModal();
        $('#addPostcard').modal('show');
    }
    /**
     * Сохранение почтовой карточки.
     * Считывает данные из формы, проверяет на ошибки и отправляет AJAX-запрос для сохранения карточки.
     */
    savePostcard() {
        // Инициализация переменных
        let postcardTitle = $('#postcard-title').val();
        let postcardDescription = $('#postcard-description').val();
        let postcardImageURL = $('#input_file_upload-image').val();
        let collectionValue = $('#collectionButton').attr('data-value');
        let categoryValue = $('#categoryButton').attr('data-value');

        // Сброс ошибок
        $('#error-list').empty();
        this.hasErrors = false;

        // Проверки на валидацию не нужны при операции перемещения
        if (!this.isMovePostCard && !this.isDeletePostCard) {
            if (postcardTitle.length < 10) {
                this.appendError('Название должно быть не менее 10 символов');
            }
            if (postcardDescription.length < 20) {
                this.appendError('Описание должно быть не менее 20 символов');
            }
        }
        if (!postcardImageURL && !this.isMovePostCard && !this.isDeletePostCard) { // Нет нужды в проверке картинки при перемещении
            this.appendError('Нужно загрузить картинку');
        }

        if (this.hasErrors) {
            return;
        }

        // Создание FormData и AJAX-запрос
        let formData = new FormData();
        formData.append('title', postcardTitle);
        formData.append('description', postcardDescription);
        formData.append('imageURL', postcardImageURL);
        formData.append('collection', collectionValue);
        formData.append('category', categoryValue);

        if (this.isSavePostCard) {
            // Сохранение открытки
            this.sendPostcardRequest(formData, '/user/save-postcard');
        } else if (this.isMovePostCard) {
            const cardId = $('input[name="card-id"]').val();
            formData.append('cardId', cardId);
            // Перемещение открытки
            this.sendPostcardRequest(formData, '/user/move-postcard');
        } else if (this.isDeletePostCard) {
            // Удаление открытки
            const cardId = $('input[name="card-id"]').val();+
            formData.append('cardId', cardId);

            this.sendPostcardRequest(formData, '/user/delete-postcard');
        } else {
            // Добавление новой открытки
            formData.append('imageURL', postcardImageURL);
            this.sendPostcardRequest(formData, '/user/add-postcard');
        }

        // Сброс значений
        $('#postcard-title').val('');
        $('#postcard-description').val('');
        $('#input_file_upload-image').val('');
        $('#addPostcard').modal('hide');
    }
    /**
     * Добавление сообщения об ошибке.
     * Добавляет сообщение об ошибке в список ошибок.
     *
     * @param {string} message - Сообщение об ошибке.
     */
    appendError(message) {
        $('#error-list').append(`<li class="list-group-item ${this.errorClasses[this.currentErrorClassIndex]}">${message}</li>`);
        this.currentErrorClassIndex = (this.currentErrorClassIndex + 1) % 2;
        this.hasErrors = true;
    }
    /**
     * Очистка списка сообщений об ошибках.
     * Удаляет все сообщения об ошибках из списка.
     */
    clearErrors() {
        $('#error-list').empty();
        this.hasErrors = false;
    };

    /**
     * Загрузка файла на сервер.
     * @param {File} file - Файл для загрузки.
     * @param {function} onSuccess - Обработчик успешной загрузки.
     * @param {function} onError - Обработчик ошибки загрузки.
     */
    uploadFile(file, onSuccess, onError) {
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: '/user/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (data) => {
                if (data.success) {
                    onSuccess(data);
                } else {
                    onError();
                }
            },
            error: () => {
                onError();
            },
            xhr: () => {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', (evt) => {
                    if (evt.lengthComputable) {
                        const percentComplete = evt.loaded / evt.total;
                        $('.upload-progress').css('width', percentComplete * 100 + '%');
                        $('.file-info-sub').text(
                            this.formatFileSize(evt.loaded) + ' из ' +
                            this.formatFileSize(evt.total) + ' (' +
                            (percentComplete * 100).toFixed(1) + '%)'
                        );
                    }
                }, false);
                return xhr;
            }
        });
    }
    /**
     * Форматирование размера файла.
     * @param {number} size - Размер файла в байтах.
     * @return {string} Отформатированный размер файла.
     */
    formatFileSize(size) {
        if(size < 1000000) {
            return Math.round(size / 1024) + 'кб';
        }
        return (size / (1024 * 1024)).toFixed(2) + 'мб';
    }
    /**
     * Загрузка файла на серверное хранилище.
     * @param {File} file - Файл для загрузки.
     * @param {function} onSuccess - Обработчик успешной загрузки.
     * @param {function} onError - Обработчик ошибки загрузки.
     */
    uploadFileToServer(file, onSuccess, onError) {
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: '/user/upload-to-server',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (data) => {
                if (data.success) {
                    onSuccess(data);
                } else {
                    onError();
                }
            },
            error: () => {
                onError();
            },
            xhr: () => {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', (evt) => {
                    if (evt.lengthComputable) {
                        const percentComplete = evt.loaded / evt.total;
                        $('.upload-progress').css('width', percentComplete * 100 + '%');
                        $('.file-info-sub').text(
                            this.formatFileSize(evt.loaded) + ' из ' +
                            this.formatFileSize(evt.total) + ' (' +
                            (percentComplete * 100).toFixed(1) + '%)'
                        );
                    }
                }, false);
                return xhr;
            }
        });
    };
    /**
     * Загрузка файла в облачное хранилище.
     * @param {string} file_id - Идентификатор файла.
     * @param {function} onSuccess - Обработчик успешной загрузки.
     * @param {function} onError - Обработчик ошибки загрузки.
     */
    uploadFileToCloud(file_id, onSuccess, onError) {

        console.log('uploadFileToCloud');
        console.log('file_id'+file_id);
        $.ajax({
            url: '/user/upload-to-cloud',
            type: 'POST',
            data: { file_id: file_id },
            success: (data) => {
                if (data.success) {
                    onSuccess(data);
                } else {
                    onError();
                }
            },
            error: () => {
                onError();
            }
        });
    };
    /**
     * Предотвращение действий по умолчанию и всплытия события.
     * @param {Event} e - Объект события.
     */
    preventDefaultActions(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    /**
     * Обработчик события перетаскивания файла.
     * При перетаскивании файла в область загрузки устанавливает этот файл в input элемент для последующей загрузки.
     * @param {Event} e - Объект события.
     */
    handleFileDrop(e) {
        this.preventDefaultActions(e);

        if (e.originalEvent.dataTransfer) {
            if (e.originalEvent.dataTransfer.files.length) {
                const file = e.originalEvent.dataTransfer.files[0];
                this.gtfsUpload.prop('files', e.originalEvent.dataTransfer.files);
                this.handleFileUpload(file);
            }
        }
    }
    /**
     * Симулирует клик по элементу для загрузки файла.
     * Вызывается при клике на элемент с id 'browse-link'.
     */
    triggerFileUpload() {
        this.gtfsUpload.trigger('click');
    }
    /**
     * Обработчик события изменения выбранного файла.
     * При изменении файла в элементе загрузки вызывает функцию для обработки этого файла.
     */
    handleFileChange() {
        if (this.gtfsUpload[0].files && this.gtfsUpload[0].files.length) {
            const file = this.gtfsUpload[0].files[0];
            this.handleFileUpload(file);
        }
    }
    /**
     * Обрабатывает загрузку файла пользователем.
     * @param {File} file - Загружаемый файл.
     */
    handleFileUpload(file) {
        this.resetUI();
        this.updateUIForUploading();

        let hasError = this.validateFile(file);
        if (hasError) {
            this.handleErrors();
            return;
        }

        this.uploadFile(file);
    }

    /**
     * Сбрасывает пользовательский интерфейс в исходное состояние.
     */
    resetUI() {
        $('#form_gtfs_url').val('');
        $('.file-upload-container-process').hide();
        $('.upload-progress-bar').hide();
        $('.label-file-upload-container').removeClass('error uploading');
    }

    /**
     * Обновляет пользовательский интерфейс для процесса загрузки.
     */
    updateUIForUploading() {
        $('.file-info').show();
        $('.popup__close').show();
        $('.file-upload-container-process').css('display', 'flex');
        $('.file-upload-container').removeClass('active').addClass('not-active');
        $('.label-file-upload-container').addClass('uploading active');
        $('.label-file-upload-container .title-status').show().text('Загрузка файла...');
    }

    /**
     * Проверяет загружаемый файл на соответствие критериям (размер, тип).
     * @param {File} file - Загружаемый файл.
     * @returns {boolean} hasError - Возвращает true, если файл не проходит проверку.
     */
    validateFile(file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webp', 'zip'];
        const fileSizeInMB = file.size / (1024 * 1024);
        let hasError = false;

        if (!validExtensions.includes(fileExtension)) {
            this.showError('Неверный тип файла. Пожалуйста, загрузите файл в формате jpg, jpeg, png, gif, mp4, webp или zip.');
            hasError = true;
        }

        if (fileSizeInMB > 1000) {
            this.showError('Размер файла превышает лимит в 1000 МБ. Пожалуйста, загрузите файл меньшего размера.');
            hasError = true;
        }

        if (file.size === 0) {
            this.showError('Неверный размер файла: 0 байт. Пожалуйста, загрузите корректные данные.');
            hasError = true;
        }

        return hasError;
    }

    /**
     * Отображает сообщение об ошибке.
     * @param {string} message - Сообщение об ошибке.
     */
    showError(message) {
        $('.error-file').html(message);
        $('.error-file').show();
    }

    /**
     * Обрабатывает ошибки, возникшие при загрузке файла.
     */
    handleErrors() {
        $('.label-file-upload-container').addClass('error');
        $('.upload-progress-bar').hide();
        $('.file-upload-container-process').hide();
        $('.label-file-upload-container .title-status').text('Загружено');
    }

    /**
     * Загружает файл на сервер и в облако.
     * @param {File} file - Загружаемый файл.
     */
    uploadFile(file) {
        $('.file-info').text('Идёт загрузка на сервер...');
        this.uploadFileToServer(file,
            (data) => this.onSuccessServer(data),
            (error) => this.onErrorServer(error)
        );
    }
    /**
     * Обрабатывает успешный ответ от сервера при загрузке файла.
     * @param {Object} serverResponse - Ответ от сервера.
     */
    onSuccessServer(serverResponse) {
        if (serverResponse && serverResponse.file_id) {
            console.log('onSuccessServer');
            $('#uploaded_file_id').val(serverResponse.file_id);
            this.uploadFileToCloud(
                serverResponse.file_id,
                (data) => this.onSuccessCloud(data),
                (error) => this.onErrorCloud(error)
            );
        } else {
            $('.error-text').html('Неожиданный ответ от сервера при загрузке на сервер.');
        }
    }
    /**
     * Обрабатывает ошибку от сервера при загрузке файла.
     * @param {Object} errorResponse - Ответ об ошибке от сервера.
     */
    onErrorServer(errorResponse) {
        $('.error-text').html('Ошибка при загрузке файла на сервер.');
        console.error("Server Upload Error:", errorResponse);
    }
    /**
     * Обрабатывает успешный ответ при загрузке файла на облако.
     * @param {Object} cloudResponse - Ответ от облачного сервера.
     */
    onSuccessCloud(cloudResponse) {
        if (cloudResponse && cloudResponse.cloud_url && !window.isCanceled && $('.file-upload-container').css('display') === 'none') {
            $('#del_file_upload-image').attr('data-file-url', cloudResponse.cloud_url);
            $('#input_file_upload-image').val(cloudResponse.cloud_url);
            $('.file-upload-container-process').hide();
            $('.file-info').text('Загружено на облако');
            console.log(cloudResponse.cloud_url);

            const fileUrl = cloudResponse.cloud_url;
            const container = $('.file-upload-container-image');
            container.css('display', 'flex');

            // Удаляем предыдущее изображение или видео, если они существуют
            container.find('img, video').remove();

            if (fileUrl.endsWith('.jpg') || fileUrl.endsWith('.png') || fileUrl.endsWith('.webp') || fileUrl.endsWith('.gif')) {
                container.append(`<img src="${fileUrl}" alt="Uploaded Image" />`);
            } else if (fileUrl.endsWith('.mp4')) {
                container.append(`<video autoplay loop muted playsinline src="${fileUrl}"></video>`);
            } else {
                $('.error-text').html('Неизвестный формат файла.');
            }
        } else if (window.isCanceled) {
            window.isCanceled = false;
        } else {
            $('.error-text').html('Неожиданный ответ от сервера при загрузке на облако.');
        }
    }

    /**
     * Обрабатывает ошибку от облака при загрузке файла.
     * @param {Object} errorResponse - Ответ об ошибке от облака.
     */
    onErrorCloud(errorResponse) {
        $('.error-text').html('Ошибка при загрузке файла на облако.');
        console.error("Cloud Upload Error:", errorResponse);
    }
    /**
     * Сбрасывает пользовательский интерфейс загрузки файла в исходное состояние.
     */
    resetUploadUI() {
        $('#gtfs_upload').val('');
        $('.file-info').text('File not selected');
        $('.upload-progress').css('width', '0%');
        $('.upload-progress-bar').hide();
        $('.file-info').hide();
        $('.file-upload-container-process').hide();
        $('.file-upload-container').removeClass('not-active').addClass('active');
        $('.label-file-upload-container').removeClass('active');
        $('.error-file').hide();
        $('.block-download-links__size.file-info-sub').text('');
        $('.label-file-upload-container.error').removeClass('error');
        $('.label-file-upload-container').addClass('uploading');
        $('.label-file-upload-container .title-status').text('Uploading file...').hide();
    }

    /**
     * Отправляет AJAX-запрос на указанный URL с переданными данными.
     * @param {string} url - URL-адрес, на который будет отправлен запрос.
     * @param {string} fileUrl - URL-адрес загружаемого файла.
     * @param {Function} onSuccess - Функция, которая будет вызвана при успешном выполнении запроса.
     * @param {Function} onError - Функция, которая будет вызвана при возникновении ошибки.
     */
    sendAjaxRequest(url, fileUrl, onSuccess, onError) {
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                fileUrl: fileUrl
            },
            success: onSuccess,
            error: onError
        });
    }
    /**
     * Удаляет локальный файл и сбрасывает UI.
     * @param {Event} event - Объект события.
     */
    handleDeleteLocalFile(event) {
        event.stopPropagation();
        const fileUrl = $(event.currentTarget).attr('data-file-url');
        this.isCanceled = true;

        this.sendAjaxRequest('/user/delete-local-file', fileUrl, function(response) {
            $('#input_file_upload-image').val('');
            if (!response.success) {
                console.error("Ошибка при удалении локального файла:", response.error);
            }
        }, function(xhr, status, error) {
            console.error("Ошибка при отправке запроса на сервер:", error);
        });

        this.resetUploadUI();
    }

    /**
     * Удаляет файл из облака и сбрасывает UI.
     * @param {Event} event - Объект события.
     */
    handleDeleteFromCloud(event) {
        event.stopPropagation();
        const fileUrl = $(event.currentTarget).attr('data-file-url');

        this.sendAjaxRequest('/user/delete-from-cloud', fileUrl, function(response) {
            $('#input_file_upload-image').val('');
            if (!response.success) {
                console.error("Ошибка при удалении файла:", response.error);
            }
        }, function(xhr, status, error) {
            console.error("Ошибка при отправке запроса на сервер:", error);
        });

        $('.file-upload-container-image').hide().children().not('#del_file_upload-image').remove();
        this.resetUploadUI();
        this.isCanceled = false;
    }

    /**
     * Удаляет локальный файл и закрывает модальное окно.
     * @param {Event} event - Объект события.
     */
    handleCloseModal(event) {
        event.stopPropagation();
        const fileUrl = $('#del_file_upload').attr('data-file-url');
        if (!fileUrl) return;
        this.isCanceled = true;

        this.sendAjaxRequest('/user/delete-local-file', fileUrl, function(response) {
            $('#input_file_upload-image').val('');
            if (!response.success) {
                console.error("Ошибка при удалении локального файла:", response.error);
            }
        }, function(xhr, status, error) {
            console.error("Ошибка при отправке запроса на сервер:", error);
        });

        this.resetUploadUI();
        $('.file-upload-container-image').hide().children().not('#del_file_upload-image').remove();
        $('#addPostcard').modal('hide');
    }

    /**
     * Инициализация счетчиков для title и description.
     */
    initializeCounters() {
        const titleInput = document.querySelector("#postcard-title");
        const titleCounter = document.querySelector("#postcard-title + .counter");
        const descriptionTextarea = document.querySelector("#postcard-description");
        const descriptionCounter = document.querySelector("#postcard-description + .counter");

        // Начальная настройка
        this.updateCounter(titleInput, titleCounter, 100);
        this.updateCounter(descriptionTextarea, descriptionCounter, 1000);

        titleInput.addEventListener('input', () => {
            this.updateCounter(titleInput, titleCounter, 100);
        });

        descriptionTextarea.addEventListener('input', () => {
            this.updateCounter(descriptionTextarea, descriptionCounter, 1000);
        });
    }

    /**
     * Обновляет счетчик для заданного элемента ввода.
     * @param {HTMLInputElement} inputElement - Элемент ввода.
     * @param {HTMLElement} counterElement - Элемент счетчика.
     * @param {number} maxLength - Максимальная длина ввода.
     */
    updateCounter(inputElement, counterElement, maxLength) {
        // Дополнительная проверка на наличие элемента ввода и элемента счетчика
        if (inputElement && counterElement) {
            const wordCount = inputElement.value.length;
            counterElement.textContent = `${wordCount}/${maxLength}`;
        } else {
            // Обработка случая, когда inputElement или counterElement отсутствует
            console.error('Ошибка: inputElement или counterElement отсутствует.');
        }
    }

    /**
     * Инициализация библиотеки Masonry.
     */
    initializeMasonry() {
        var elem = document.querySelector('.grid');
        window.msnry = new Masonry(elem, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-item',
            percentPosition: true,
            gutter: 20
        });

        imagesLoaded(elem).on('progress', function() {
            window.msnry.layout();
        });

        imagesLoaded(elem).on('always', function() {
            window.msnry.layout();
        });
    }

    /**
     * Получение и обновление списка коллекций.
     */
    fetchCollections() {
        $.ajax({
            url: '/user/get-collections',
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    this.updateDropdownContent(".collection-buttons .dropdown-menu", response.data);
                    if (this.isMovePostCard) {
                        this.setCollectionAndCategory('collection');
                    }
                } else {
                    console.error("Ошибка при получении списка коллекций:", response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Ошибка при отправке запроса на сервер:", error);
            }
        });
    }

    /**
     * Получение и обновление списка категорий.
     */
    fetchCategories() {
        $.ajax({
            url: '/user/get-categories',
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    this.updateDropdownContent(".category-buttons .dropdown-menu", response.data, true);
                    if (this.isMovePostCard) {
                        this.setCollectionAndCategory('category');
                    }
                } else {
                    console.error("Ошибка при получении списка категорий:", response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Ошибка при отправке запроса на сервер:", error);
            }
        });
    }

    /**
     * Обновление содержимого выпадающего списка.
     * @param {string} dropdownId - Идентификатор элемента выпадающего списка.
     * @param {Array} list - Список элементов для добавления.
     * @param {boolean} isCategory - Является ли элемент категорией.
     */
    updateDropdownContent(dropdownId, list, isCategory = false) {
        let $dropdown = $(dropdownId);
        $dropdown.empty();

        // Добавляем ссылку для создания новой категории или коллекции
        if (isCategory) {
            $dropdown.append('<a id="createCategoryButtonAddCard" class="dropdown-item" data-value="new">Создать категорию</a>');
        } else {
            $dropdown.append('<a id="createCollectionButtonAddCard" class="dropdown-item" data-value="new">Создать коллекцию</a>');
        }

        $dropdown.append('<div class="dropdown-divider"></div>');

        list.forEach(item => {
            $dropdown.append(`<a class="dropdown-item" data-value="${item.id}">${item.name}</a>`);
        });
    }

    /**
     * Обработчик события клика по элементу выпадающего списка категории или коллекции.
     * @param {Event} event - Объект события.
     */
    handleDropdownItemClick(event) {
        let $this = $(event.target);
        let name = $this.text();
        let value = $this.data('value');

        if ($this.closest('.dropdown-menu').attr('aria-labelledby') === 'dropdownCollectionButton') {
            $('#collectionButton').text('Коллекция: ' + name).attr('data-value', value);
        } else if ($this.closest('.dropdown-menu').attr('aria-labelledby') === 'dropdownCategoryButton') {
            $('#categoryButton').text('Категория: ' + name).attr('data-value', value);
        }
    }

    /**
     * Отображает форму создания новой коллекции.
     */
    showCollectionCreationForm() {
        $('.collection-buttons').hide();
        $('.new-collection-form').removeClass('d-none').show();
    }

    /**
     * Отображает форму создания новой категории.
     */
    showCategoryCreationForm() {
        $('.category-buttons').hide();
        $('.new-category-form').removeClass('d-none').show();
    }

    /**
     * Создание и добавление новой коллекции.
     * @param {Event} event - Объект события.
     */
    createAndAddCollectionHandler(event) {
        let collectionName = $('.new-collection-form .form-control').val();

        if(collectionName.trim() === "") {
            showToast("Название коллекции не может быть пустым.", 'danger', 15000);
            return;
        }

        this.createAndAddCollection(collectionName, '#collectionButton', function(success) {
            if(success) {
                $('.new-collection-form').addClass('d-none').hide();
                $('.collection-buttons').show();
            }
        });
    }

    /**
     * Создание и добавление новой категории.
     * @param {Event} event - Объект события.
     */
    createAndAddCategoryHandler(event) {
        let categoryName = $('.new-category-form .form-control').val();

        if(categoryName.trim() === "") {
            showToast("Название категории не может быть пустым.", 'danger', 15000);
            return;
        }

        this.createAndAddCategory(categoryName, '#createCategoryButton', function(success) {
            if(success) {
                $('.new-category-form').addClass('d-none').hide();
                $('.category-buttons').show();
            }
        });
    }

    /**
     * Скрывает форму создания новой коллекции.
     */
    hideCollectionCreationForm() {
        $('.new-collection-form').addClass('d-none').hide();
        $('.collection-buttons').show();
    }

    /**
     * Скрывает форму создания новой категории.
     */
    hideCategoryCreationForm() {
        $('.new-category-form').addClass('d-none').hide();
        $('.category-buttons').show();
    }

    /**
     * Обновляет пользовательский интерфейс с новыми данными коллекции.
     *
     * @param {Object} data - Данные новой коллекции.
     * @param {string} username - Имя пользователя.
     */
    updateUIWithNewCollection(data, username) {
        showToast(data.message, 'success', 1500);

        let newCollectionItem = $('<a></a>')
            .addClass('dropdown-item')
            .attr('data-value', data.newCollection.id)
            .text(data.newCollection.name);
        $(".collection-buttons .dropdown-menu").append(newCollectionItem);

        $('#collectionButton').text('Коллекция: ' + data.newCollection.name).attr('data-value', data.newCollection.id);

        let newCollection = $('<a></a>')
            .attr('href', '/' + username + '/collection/' + data.newCollection.id)
            .addClass('btn btn-secondary btn-sm')
            .attr('title', data.newCollection.name)
            .text(data.newCollection.name)
            .append('<span class="badge bg-secondary">0</span>');
        newCollection.insertAfter('#collections-list a:last-child');
    }

    /**
     * Создание и добавление новой коллекции через AJAX.
     *
     * @param {string} collectionName - Имя новой коллекции.
     * @param {string} usernameElementId - ID элемента, где можно найти имя пользователя.
     * @param {Function} callback - Функция обратного вызова после выполнения AJAX-запроса.
     */
    createAndAddCollection(collectionName, usernameElementId, callback) {
        let username = $('#createCollectionButton').data('username');
        $.ajax({
            url: '/user/create-collection',
            type: 'POST',
            data: { name: collectionName },
            success: (data) => {
                if (data.success) {
                    this.updateUIWithNewCollection(data, username);
                    callback(true);
                } else {
                    showToast(data.message, 'danger', 15000);
                    callback(false);
                }
            }
        });
    }


    /**
     * Показывает модальное окно для создания новой категории.
     */
    showModalCreateCategory() {
        const myModal = new bootstrap.Modal(document.getElementById('createCategory'), {});
        myModal.show();
    }

    /**
     * Обновляет пользовательский интерфейс с новыми данными категории.
     *
     * @param {Object} data - Данные новой категории.
     * @param {string} username - Имя пользователя.
     */
    updateUIWithNewCategory(data, username) {
        showToast(data.message, 'success', 1500);

        let newCategoryItem = $('<a></a>')
            .addClass('dropdown-item')
            .attr('data-value', data.newCategory.id)
            .text(data.newCategory.name);
        $(".category-buttons .dropdown-menu").append(newCategoryItem);

        $('#categoryButton').text('Категория: ' + data.newCategory.name).attr('data-value', data.newCategory.id);

        let newCategory = $('<a></a>')
            .attr('href', '/' + username + '/category/' + data.newCategory.id)
            .addClass('btn btn-info btn-sm')
            .attr('title', data.newCategory.name)
            .text(data.newCategory.name)
            .append('<span class="badge bg-info">0</span>');
        newCategory.insertAfter('#categories-list a:last-child');
    }

    /**
     * Создание и добавление новой категории через AJAX.
     *
     * @param {string} categoryName - Имя новой категории.
     * @param {string} usernameElementId - ID элемента, где можно найти имя пользователя.
     * @param {Function} callback - Функция обратного вызова после выполнения AJAX-запроса.
     */
    createAndAddCategory(categoryName, usernameElementId, callback) {
        let username = $(usernameElementId).data('username');
        $.ajax({
            url: '/user/create-category',
            type: 'POST',
            data: { name: categoryName },
            success: (data) => {
                if (data.success) {
                    this.updateUIWithNewCategory(data, username);
                    callback(true);  // Если успешно создано
                } else {
                    showToast(data.message, 'danger', 15000);
                    callback(false);  // Если ошибка
                }
            }
        });
    }


    /**
     * Переключает класс 'active' для элемента карточки.
     *
     * @param {Event} event - Объект события клика.
     */
    toggleActiveCard(event) {
        $(event.currentTarget).toggleClass('active');
    }

    /**
     * Обновление раскладки masonry.
     */
    updateMasonry555() {
        setTimeout(() => {
            window.msnry.layout();
        }, 555);
    }

    /**
     * Обработка события наведения мыши на карточку.
     *
     * @param {Event} event - Объект события наведения мыши.
     */
    handleMouseOverCard(event) {
        event.currentTarget.classList.add('is-active');
        window.msnry.layout();
        this.updateMasonry555();
    }

    /**
     * Обработка события ухода мыши с карточки.
     *
     * @param {Event} event - Объект события ухода мыши.
     */
    handleMouseOutCard(event) {
        event.currentTarget.classList.remove('is-active');
        window.msnry.layout();
        this.updateMasonry555();
    }
    /**
     * Отображение модального окна для сохранения открытки.
     * Метод вызывается при клике на кнопку "Сохранить" и открывает модальное окно,
     * запрашивая детали посткарты по её id.
     *
     * @param {Event} event - Объект события клика.
     */
    showSavePostcardModal(event) {
        this.isSavePostCard = true;
        this.isMovePostCard = false;
        this.initializeFormPostcardModal();
        $('#addPostcard').modal('show');
    }
    /**
     * Инициализация формы для загрузки или сохранения открытки.
     * Метод устанавливает класс 'active' или 'not-active' для элемента с классом 'file-upload-container'
     * в зависимости от значения this.isSavePostCard.
     */

    initializeFormPostcardModal() {
        this.collectionId = 0;
        this.categoryId = 0;
        this.fetchCollections();
        this.fetchCategories();
        this.clearErrors();
        if (this.isSavePostCard || this.isMovePostCard || this.isDeletePostCard) {
            const id = $(event.target).data('id');
            this.collectionId = $(event.target).data('collection-id');
            this.categoryId = $(event.target).data('category-id');
            const fileUploadContainerImage = document.querySelector('.file-upload-container-image');
            if (this.isDeletePostCard) {
                fileUploadContainerImage.classList.add('file-upload-container-image-delete');
                document.querySelector(".full-screen-container").style.display = "block"; // показать
            } else {
                fileUploadContainerImage.classList.remove('file-upload-container-image-delete');
                document.querySelector(".full-screen-container").style.display = "none";  // скрытьрыть
            }

            if (this.isMovePostCard) {
                // Установка скрытого поля card-id
                $('input[name="card-id"]').val(id);
                $('#addPostcardModalLabel').text('Переместить открытку');
                $("#save-postcard-btn").text("Переместить");

                document.getElementById('postcard-title').parentNode.style.display = 'none';
                document.getElementById('postcard-description').parentNode.style.display = 'none';
            } else if (this.isDeletePostCard) {
                // Установка скрытого поля card-id
                $('input[name="card-id"]').val(id);
                // Установка интерфейса для удаления
                $('#addPostcardModalLabel').text('Удалить открытку');
                $("#save-postcard-btn").text("Удалить");

                document.getElementById('postcard-title').parentNode.style.display = 'none';
                document.getElementById('postcard-description').parentNode.style.display = 'none';
                document.querySelectorAll('.collection-buttons').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.category-buttons').forEach(el => el.style.display = 'none');
            } else {
                const title = $(event.target).data('title');
                const description = $(event.target).data('description');

                $('#postcard-title').val(title);
                $('#postcard-description').val(description);
                $('#addPostcardModalLabel').text('Сохранить открытку');
                $("#save-postcard-btn").text("Сохранить");

                document.getElementById('postcard-title').parentNode.style.display = 'block';
                document.getElementById('postcard-description').parentNode.style.display = 'block';
            }

            const src = $(event.target).data('src');
            $('.file-upload-container').removeClass('active').addClass('not-active');
            $('#del_file_upload-image').hide();

            const container = $('.file-upload-container-image');
            container.css('display', 'flex').css('border', 'none');
            container.find('img, video').remove();
            container.append(`<img src="${src}" alt="Uploaded Image" />`);
            $('#input_file_upload-image').val(src);
        } else {
            document.getElementById('postcard-title').parentNode.style.display = 'block';
            document.getElementById('postcard-description').parentNode.style.display = 'block';
            $('.file-upload-container').removeClass('not-active').addClass('active');
            $('#addPostcardModalLabel').text('Добавить открытку');
            $("#save-postcard-btn").text("Добавить");

            $('#del_file_upload-image').show();

            const container = $('.file-upload-container-image');
            container.css('display', 'flex').css('border', '2px dashed rgba(255,255,255,.7)');
            container.find('img, video').remove();
            $('.file-upload-container-image').hide().children().not('#del_file_upload-image').remove();
        }
    }

    /**
     * Отправка AJAX-запроса для сохранения или добавления открытки.
     * Метод использует jQuery для выполнения POST-запроса с переданными данными в форме FormData.
     * При успешном выполнении запроса, вызывается функция showToast для отображения сообщения.
     *
     * @param {FormData} formData - Данные формы, которые необходимо отправить.
     * @param {string} url - URL-адрес, на который необходимо отправить запрос.
     */
    sendPostcardRequest(formData, url) {
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    showToast(data.message, 'success', 1500);
                    if (this.isDeletePostCard) {
                        $('#grid-item-'+data.cardId).hide()
                    }
                } else {
                    showToast(data.message, 'danger', 15000);
                }
            }
        });
    }

    /**
     * Отображение модального окна для перемещения открытки.
     * Метод вызывается при клике на кнопку "Переместить" и открывает модальное окно,
     * запрашивая детали открытки по её id для операции перемещения.
     *
     * @param {Event} event - Объект события клика.
     */
    showMovePostcardModal(event) {
        this.isMovePostCard = true;
        this.isSavePostCard = false;
        this.initializeFormPostcardModal();
        $('#addPostcard').modal('show');
    }

    /**
     * Устанавливает выбранный пункт в выпадающем меню на основе переданного `id`.
     * Метод проходит по всем пунктам выпадающего меню и выбирает тот пункт, который
     * соответствует указанному `id`. Выбранный пункт отображается на кнопке меню.
     *
     * @param {string} dropdownButtonId - ID кнопки выпадающего меню.
     * @param {string} dropdownMenuId - селектор меню, содержащего пункты для выбора.
     * @param {number} selectedId - ID пункта, который нужно выбрать.
     */

    setDropdownSelection(dropdownButtonId, dropdownMenuId, selectedId) {
        // Сброс текущего выбора
        $(`#${dropdownButtonId}`).text('Не выбрано').attr('data-value', '');

        // Пройдемся по всем пунктам в выпадающем меню
        $(`${dropdownMenuId} .dropdown-item`).each(function() {
            const itemId = $(this).data('value');
            const itemName = $(this).text();

            // Если текущий пункт соответствует переданному id, обновим текст кнопки
            if (itemId === selectedId) {
                $(`#${dropdownButtonId}`).text(itemName).attr('data-value', itemId);
                return false;  // прерываем цикл
            }
        });
    }

    /**
     * Устанавливает выбранные пункты для выпадающих меню "Коллекция" и "Категория"
     * на основе переданного параметра.
     *
     * @param {string} type - Тип выпадающего меню, которое нужно обновить.
     * Может принимать значения 'collection', 'category' или 'both'.
     */
    setCollectionAndCategory(type) {
        if (type === 'collection' || type === 'both') {
            this.setDropdownSelection('collectionButton', '.collection-buttons .dropdown-menu', this.collectionId);
        }

        if (type === 'category' || type === 'both') {
            this.setDropdownSelection('categoryButton', '.category-buttons .dropdown-menu', this.categoryId);
        }
    }

    /**
     * Отображение модального окна для удаления открытки.
     * Метод вызывается при клике на кнопку "Удалить" и открывает модальное окно,
     * запрашивая подтверждение для удаления.
     *
     * @param {Event} event - Объект события клика.
     */
    showDeletePostcardModal(event) {
        this.isSavePostCard = false;
        this.isMovePostCard = false;
        this.isDeletePostCard = true;
        this.initializeFormPostcardModal();
        $('#addPostcard').modal('show');

        const postcardName = "Открытка такая то";
        const message = `Вы действительно хотите удалить "${postcardName}"?`;
    }

/*.del-button, .user-images .nav.nav-pills*/
}

$(document).ready(function() {
    new CardManager();
});
