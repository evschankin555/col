window.isCanceled = false;

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
    let username = $('#createCollectionButton').data('username');

    $.ajax({
        url: '/user/create-collection',
        type: 'POST',
        data: { name: collectionName },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);

                // Добавьте новую коллекцию в список
                let newCollection = $('<a></a>')
                    .attr('href', '/' + username + '/collection/' + data.newCollection.id)
                    .addClass('btn btn-secondary btn-sm')
                    .attr('title', data.newCollection.name)
                    .text(data.newCollection.name)
                    .append('<span class="badge bg-secondary">0</span>');
                /*$('#collections-list').append(newCollection);*/
                newCollection.insertAfter('#collections-list a:last-child');
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    $('#new-collection-name').val('');
    $('#createCollection').modal('hide');
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

$('#addPostcardButton').click(function() {
    $('#addPostcard').modal('show');
    window.isCanceled = false;
});

$('#save-postcard-btn').click(function() {
    let postcardTitle = $('#postcard-title').val();
    let postcardDescription = $('#postcard-description').val();
    let postcardImage = $('#postcard-image').prop('files')[0];

    let formData = new FormData();
    formData.append('title', postcardTitle);
    formData.append('description', postcardDescription);
    formData.append('image', postcardImage);

    $.ajax({
        url: '/user/add-postcard',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success', 1500);
                // Здесь можно добавить действия после успешной загрузки, например, обновление списка открыток на странице
            } else {
                showToast(data.message, 'danger', 15000);
            }
        }
    });

    $('#postcard-title').val('');
    $('#postcard-description').val('');
    $('#postcard-image').val('');
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



