$(document).on('click', '#subscribe-btn', function() {
    var button = $(this);
    $.ajax({
        url: '/user/subscribe?username=' + encodeURIComponent(button.data('username')),
        type: 'POST',
        success: function(data) {
            if (data.success) {
                button.text('Отписаться')
                    .attr('id', 'unsubscribe-btn')
                    .toggleClass('btn-secondary btn-danger');
                $('.subscribersCount').text(data.subscribersCount);
                $('.subscriptionsCount').text(data.subscriptionsCount);
                showToast('Вы подписаны.', 'success', 1500);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
    return false;
});

$(document).on('click', '#unsubscribe-btn', function() {
    var button = $(this);
    $.ajax({
        url: '/user/unsubscribe?username=' + encodeURIComponent(button.data('username')),
        type: 'POST',
        success: function(data) {
            if (data.success) {
                button.text('Подписаться')
                    .attr('id', 'subscribe-btn')
                    .toggleClass('btn-danger btn-secondary');
                $('.subscribersCount').text(data.subscribersCount);
                $('.subscriptionsCount').text(data.subscriptionsCount);
                showToast('Вы отписаны.', 'success', 2000);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });
    return false;
});
let createCollectionButton = document.getElementById('createCollectionButton');
if (createCollectionButton !== null) {
    createCollectionButton.addEventListener('click', function () {
        myModal = new bootstrap.Modal(document.getElementById('createCollection'), {});
        myModal.show();
    });
}

$('#create-collection-btn').click(function() {
    let collectionName = $('#new-collection-name').val();

    createAndAddCollection(collectionName, '#createCollectionButton', function(success) {
        if (success) {
            $('#new-collection-name').val('');
            $('#createCollection').modal('hide');
        }
    });
});


$('#deleteCollectionButton').click(function() {
    let collectionId = $(this).data('collection-id');
    let username = $(this).data('username');
    $('#delete-collection-id').val(collectionId);
    $('#delete-collection-username').val(username);

    let myModal = new bootstrap.Modal(document.getElementById('deleteCollection'), {});
    myModal.show();
});

$('#confirm-delete-btn').click(function() {
    let collectionId = $('#delete-collection-id').val();
    let username = $('#delete-collection-username').val();

    $.ajax({
        url: '/user/delete-collection',
        type: 'POST',
        data: { id: collectionId },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
                // Удалите коллекцию из списка
                $('#' + collectionId).remove();
                setTimeout(function() {
                    window.location.href = "/" + username;
                }, 1000);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteCollection'));
    deleteModal.hide();
});
$('#addPostcard').on('shown.bs.modal', function () {
    const buttons = document.querySelectorAll('.collection-buttons, .category-buttons');
    buttons.forEach(btn => {
        const width = btn.offsetWidth;
        const dropdownMenu = btn.querySelector('.dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.style.width = `${width}px`;
        }
    });
});
$('#addPostcardButton').click(function() {
    $('#addPostcard').modal('show');
    window.isCanceled = false;
    fetchCollections();
    fetchCategories();
});
$('#save-postcard-btn').click(function() {
    let postcardTitle = $('#postcard-title').val();
    let postcardDescription = $('#postcard-description').val();
    let postcardImageURL = $('#input_file_upload-image').val();
    let collectionValue = $('#collectionButton').data('value');
    let categoryValue = $('#categoryButton').data('value');

    // Сбросим ошибки
    $('#error-list').empty();

    let errorClasses = ['list-group-item-primary', 'list-group-item-secondary'];
    let currentErrorClassIndex = 0;
    let hasErrors = false;

    function appendError(message) {
        $('#error-list').append(`<li class="list-group-item ${errorClasses[currentErrorClassIndex]}">${message}</li>`);
        currentErrorClassIndex = (currentErrorClassIndex + 1) % 2;
        hasErrors = true;
    }

    if(postcardTitle.length < 10) {
        appendError('Название должно быть не менее 10 символов');
    }
    if(postcardDescription.length < 20) {
        appendError('Описание должно быть не менее 20 символов');
    }
    if(!postcardImageURL) {
        appendError('Нужно загрузить картинку');
    }

    if(hasErrors) {
        return;
    }

    let formData = new FormData();
    formData.append('title', postcardTitle);
    formData.append('description', postcardDescription);
    formData.append('imageURL', postcardImageURL);
    formData.append('collection', collectionValue);
    formData.append('category', categoryValue);

    $.ajax({
        url: '/user/add-postcard',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    $('#postcard-title').val('');
    $('#postcard-description').val('');
    $('#input_file_upload-image').val('');
    $('#addPostcard').modal('hide');
});
function uploadFile(file, onSuccess, onError) {
    const formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: '/user/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.success) {
                onSuccess(data);
            } else {
                onError();
            }
        },
        error: function () {
            onError();
        },
        xhr: function () {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function (evt) {
                if (evt.lengthComputable) {
                    const percentComplete = evt.loaded / evt.total;

                    // Обновление индикатора прогресса
                    $('.upload-progress').css('width', percentComplete * 100 + '%');
                    $('.file-info-sub').text(
                        formatFileSize(evt.loaded) + ' из ' +
                        formatFileSize(evt.total) + ' (' +
                        (percentComplete * 100).toFixed(1) + '%)'
                    );
                }
            }, false);
            return xhr;
        }
    });
}
function formatFileSize(size) {
    // Если меньше 1 000 000 байт, отображаем в килобайтах
    if(size < 1000000) {
        return Math.round(size / 1024) + 'кб';
    }
    // Иначе отображаем в мегабайтах
    return (size / (1024 * 1024)).toFixed(2) + 'мб';
}
function uploadFileToServer(file, onSuccess, onError) {
    const formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: '/user/upload-to-server',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.success) {
                onSuccess(data);
            } else {
                onError();
            }
        },
        error: function () {
            onError();
        },
        xhr: function () {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function (evt) {
                if (evt.lengthComputable) {
                    const percentComplete = evt.loaded / evt.total;
                    $('.upload-progress').css('width', percentComplete * 100 + '%');
                    $('.file-info-sub').text(
                        formatFileSize(evt.loaded) + ' из ' +
                        formatFileSize(evt.total) + ' (' +
                        (percentComplete * 100).toFixed(1) + '%)'
                    );
                }
            }, false);
            return xhr;
        }
    });
}
function uploadFileToCloud(file_id, onSuccess, onError) {
    $.ajax({
        url: '/user/upload-to-cloud',
        type: 'POST',
        data: { file_id: file_id },
        success: function (data) {
            if (data.success) {
                onSuccess(data);
            } else {
                onError();
            }
        },
        error: function () {
            onError();
        }
    });
}
const gtfsUpload = $('#gtfs_upload');
const fileUploadContainer = $('.file-upload-container');
fileUploadContainer.on('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
});
fileUploadContainer.on('dragenter', function(e) {
    e.preventDefault();
    e.stopPropagation();
});
fileUploadContainer.on('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();

    if (e.originalEvent.dataTransfer) {
        if (e.originalEvent.dataTransfer.files.length) {
            e.preventDefault();
            e.stopPropagation();
            const file = e.originalEvent.dataTransfer.files[0];
            gtfsUpload.prop('files', e.originalEvent.dataTransfer.files);
            handleFileUpload(file); // Обработчик handleFileUpload остается тем же
        }
    }
});
$('#browse-link').on('click', function() {
    gtfsUpload.trigger('click');
});
gtfsUpload.on('change', function() {
    if (this.files && this.files.length) {
        const file = this.files[0];
        handleFileUpload(file); // Обработчик handleFileUpload остается тем же
    }
});
function handleFileUpload(file) {
    // Если пользователь выбирает файл, очищаем поле URL
    $('#form_gtfs_url').val('');
    $('.file-info').text(file.name);
    $('.popup__close').show();
    $('.file-info').show();
    $('.upload-progress-bar').show();
    $('.file-upload-container-process').css('display', 'flex');
    $('.file-upload-container').removeClass('active').addClass('not-active');
    $('.label-file-upload-container').addClass('uploading');
    $('.label-file-upload-container .title-status').show().text('Загрузка файла...');

    $('.label-file-upload-container').addClass('active');
    let hasError = false;

    // Проверяем, является ли файл одним из допустимых типов
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webp', 'zip'];
    if (!validExtensions.includes(fileExtension)) {
        $('.error-file').html('Неверный тип файла. Пожалуйста, загрузите файл в формате jpg, jpeg, png, gif, mp4, webp или zip.');
        $('.error-file').show();
        hasError = true;
    }

    // Проверяем, не превышает ли файл размер в 1000 MB
    const fileSizeInMB = file.size / (1024 * 1024);
    if (fileSizeInMB > 1000) {
        $('.error-file').html('Размер файла превышает лимит в 1000 МБ. Пожалуйста, загрузите файл меньшего размера.');
        $('.error-file').show();
        hasError = true;
    }

    if (file.size === 0) {
        $('.error-file').html('Неверный размер файла: 0 байт. Пожалуйста, загрузите корректные данные.');
        $('.error-file').show();
        hasError = true;
    }

    if (hasError) {
        $('.label-file-upload-container').addClass('error');
        $('.upload-progress-bar').hide();
        $('.file-upload-container-process').hide();
        $('.label-file-upload-container').addClass('uploading');
        $('.label-file-upload-container .title-status').text('Загружено');
        return;
    }

        // Первый шаг: загрузка файла на сервер
    $('.file-info').text('Идёт загрузка на сервер...');

    uploadFileToServer(file,
            // При успешной загрузке на сервер:
            function (serverResponse) {
                if (serverResponse && serverResponse.file_id) {
                    $('#uploaded_file_id').val(serverResponse.file_id);
                    $('.upload-progress').css('width', '0%');
                    $('.file-info-sub').text(formatFileSize(file.size));
                    $('.file-info').text('Идёт загрузка в облако...');
                    $('#del_file_upload').attr('data-file-url', serverResponse.file_id);

                        // Второй шаг: загрузка файла на облако
                    uploadFileToCloud(serverResponse.file_id,
                        // При успешной загрузке на облако:
                        function(cloudResponse) {
                            if (cloudResponse && cloudResponse.cloud_url && window.isCanceled == false && $('.file-upload-container').css('display') == 'none') {
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
                                    container.append('<img src="' + fileUrl + '" alt="Uploaded Image" />');
                                } else if (fileUrl.endsWith('.mp4')) {
                                    container.append('<video autoplay loop muted playsinline src="' + fileUrl + '"></video>');
                                } else {
                                    $('.error-text').html('Неизвестный формат файла.');
                                }

                            } else if(window.isCanceled) {
                                window.isCanceled = false;
                            }else{
                                $('.error-text').html('Неожиданный ответ от сервера при загрузке на облако.');
                            }
                        },
                        // При ошибке загрузки на облако:
                        function(errorResponse) {
                            $('.error-text').html('Ошибка при загрузке файла на облако.');
                            console.error("Cloud Upload Error:", errorResponse);  // Логирование ошибки в консоль для отладки
                        }
                    );
                } else {
                    $('.error-text').html('Неожиданный ответ от сервера при загрузке на сервер.');
                }
            },
            // При ошибке загрузки на сервер:
            function (errorResponse) {
                $('.error-text').html('Ошибка при загрузке файла на сервер.');
                console.error("Server Upload Error:", errorResponse);  // Логирование ошибки в консоль для отладки
            }
        );



}
function onSuccess(data) {
    // Handle success case
}
function onError() {
    // Handle error case
}
function resetUploadUI() {
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
function sendAjaxRequest(url, fileUrl, onSuccess, onError) {
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
$('#del_file_upload').on('click', function (event) {
    event.stopPropagation();
    const fileUrl = $(this).attr('data-file-url');
    window.isCanceled = true;

    sendAjaxRequest('/user/delete-local-file', fileUrl, function(response) {
        $('#input_file_upload-image').val('');
        if (!response.success) {
            console.error("Ошибка при удалении локального файла:", response.error);
        }
    }, function(xhr, status, error) {
        console.error("Ошибка при отправке запроса на сервер:", error);
    });

    resetUploadUI();
});
$('#del_file_upload-image').on('click', function (event) {
    event.stopPropagation();
    const fileUrl = $(this).attr('data-file-url');

    sendAjaxRequest('/user/delete-from-cloud', fileUrl, function(response) {
        $('#input_file_upload-image').val('');
        if (!response.success) {
            console.error("Ошибка при удалении файла:", response.error);
        }
    }, function(xhr, status, error) {
        console.error("Ошибка при отправке запроса на сервер:", error);
    });

    $('.file-upload-container-image').hide().children().not('#del_file_upload-image').remove();
    resetUploadUI();
    window.isCanceled = false;
});
$('#closeModalButtonAddPostcard').on('click', function (event) {
    event.stopPropagation();

    const fileUrl = $('#del_file_upload').attr('data-file-url');
    if (!fileUrl) return;
    window.isCanceled = true;

    sendAjaxRequest('/user/delete-local-file', fileUrl, function(response) {
        $('#input_file_upload-image').val('');
        if (!response.success) {
            console.error("Ошибка при удалении локального файла:", response.error);
        }
    }, function(xhr, status, error) {
        console.error("Ошибка при отправке запроса на сервер:", error);
    });

    resetUploadUI();
    $('.file-upload-container-image').hide().children().not('#del_file_upload-image').remove();
    $('#addPostcard').modal('hide');
});
document.addEventListener("DOMContentLoaded", function() {
    const titleInput = document.querySelector("#postcard-title");
    const titleCounter = document.querySelector("#postcard-title + .counter");
    const descriptionTextarea = document.querySelector("#postcard-description");
    const descriptionCounter = document.querySelector("#postcard-description + .counter");

    function updateCounter(inputElement, counterElement, maxLength) {
        const wordCount = inputElement.value.length;
        counterElement.textContent = `${wordCount}/${maxLength}`;
    }

    titleInput.addEventListener('input', () => {
        updateCounter(titleInput, titleCounter, 100);
    });

    descriptionTextarea.addEventListener('input', () => {
        updateCounter(descriptionTextarea, descriptionCounter, 1000);
    });

    // Initial setup
    updateCounter(titleInput, titleCounter, 100);
    updateCounter(descriptionTextarea, descriptionCounter, 1000);

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

});
window.onload = function() {
    window.msnry.layout();
};
function updateDropdownContent(dropdownId, list, isCategory = false) {
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
        $dropdown.append(`<a class="dropdown-item" data-value="${item.id}">${item.name}</a>`); // Изменим item.value на item.id
    });
}
function fetchCollections() {
    $.ajax({
        url: '/user/get-collections',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                updateDropdownContent(".collection-buttons .dropdown-menu", response.data);
            } else {
                console.error("Ошибка при получении списка коллекций:", response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error("Ошибка при отправке запроса на сервер:", error);
        }
    });
}
function fetchCategories() {
    $.ajax({
        url: '/user/get-categories',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                updateDropdownContent(".category-buttons .dropdown-menu", response.data, true);
            } else {
                console.error("Ошибка при получении списка категорий:", response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error("Ошибка при отправке запроса на сервер:", error);
        }
    });
}
$(document).on('click', '.category-buttons .dropdown-item:not(#createCategoryButtonAddCard), .collection-buttons .dropdown-item:not(#createCollectionButtonAddCard)', function() {
    let $this = $(this);
    let name = $this.text();
    let value = $this.data('value');

    if ($this.closest('.dropdown-menu').attr('aria-labelledby') === 'dropdownCollectionButton') {
        $('#collectionButton').text('Коллекция: ' + name).attr('data-value', value);
    } else if ($this.closest('.dropdown-menu').attr('aria-labelledby') === 'dropdownCategoryButton') {
        $('#categoryButton').text('Категория: ' + name).attr('data-value', value);
    }
});
$(document).on('click', '#createCollectionButtonAddCard', function() {
    $('.collection-buttons').hide();
    $('.new-collection-form').removeClass('d-none').show();
});
$(document).on('click', '#createCategoryButtonAddCard', function() {
    $('.category-buttons').hide();
    $('.new-category-form').removeClass('d-none').show();
});
$(document).on('click', '#createCollectionButtonForm', function() {
    let collectionName = $('.new-collection-form .form-control').val();

    if(collectionName.trim() === "") {
        showToast("Название коллекции не может быть пустым.", 'danger', 15000);
        return;
    }

    createAndAddCollection(collectionName, '#collectionButton', function(success) {
        if(success) {
            $('.new-collection-form').addClass('d-none').hide();
            $('.collection-buttons').show();
        }
    });
});
$(document).on('click', '#createCategoryButtonForm', function() {
    let categoryName = $('.new-category-form .form-control').val();

    // Проверяем, что название категории не пустое
    if(categoryName.trim() === "") {
        showToast("Название категории не может быть пустым.", 'danger', 15000);
        return;
    }

    // Отправляем запрос на сервер с названием новой категории
    createAndAddCategory(categoryName, '#categoryButton', function(success) {
        if(success) {
            // После успешного создания обновляем интерфейс:
            // 1. Скрываем форму создания категории
            $('.new-category-form').addClass('d-none').hide();

            // 2. Показываем кнопки категории
            $('.category-buttons').show();
        }
    });
});
$(document).on('click', '#cancelCollectionButton', function() {
    $('.new-collection-form').addClass('d-none').hide();
    $('.collection-buttons').show();
});
$(document).on('click', '#cancelCategoryButton', function() {
    $('.new-category-form').addClass('d-none').hide();
    $('.category-buttons').show();
});
function createAndAddCollection(collectionName, usernameElementId, callback) {
    let username = $('#createCollectionButton').data('username');
    $.ajax({
        url: '/user/create-collection',
        type: 'POST',
        data: { name: collectionName },
        success: function(data) {
            if (data.success) {
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

                callback(true);  // Если успешно создано
            } else {
                showToast(data.message, 'danger', 15000);
                callback(false);  // Если ошибка
            }
        }
    });
}
let createCategoryButton = document.getElementById('createCategoryButton');
if (createCategoryButton !== null) {
    createCategoryButton.addEventListener('click', function () {
        myModal = new bootstrap.Modal(document.getElementById('createCategory'), {});
        myModal.show();
    });
}
$('#create-category-btn').click(function() {
    let categoryName = $('#new-category-name').val();

    createAndAddCategory(categoryName, '#createCategoryButton', function(success) {
        if (success) {
            $('#new-category-name').val('');
            $('#createCategory').modal('hide');
        }
    });
});
function createAndAddCategory(categoryName, usernameElementId, callback) {
    let username = $(usernameElementId).data('username');
    $.ajax({
        url: '/user/create-category',
        type: 'POST',
        data: { name: categoryName },
        success: function(data) {
            if (data.success) {
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

                callback(true);  // Если успешно создано
            } else {
                showToast(data.message, 'danger', 15000);
                callback(false);  // Если ошибка
            }
        }
    });
}
$('.user-images .card').on('click', function() {
    $(this).toggleClass('active');
});
function updateMasonry555(){
    setTimeout(function (){
        window.msnry.layout();
    }, 555);
};
document.querySelectorAll('.js-card').forEach(function(card) {
    card.addEventListener('mouseover', function() {
        this.classList.add('is-active');
        window.msnry.layout();
        updateMasonry555();
    });
    card.addEventListener('mouseout', function() {
        this.classList.remove('is-active');
        window.msnry.layout();
        updateMasonry555();
    });
});