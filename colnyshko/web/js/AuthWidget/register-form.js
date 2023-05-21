$('form#register-form').on('submit', function(e) {
    e.preventDefault(); // останавливаем стандартное поведение формы

    var data = $(this).serialize(); // собираем данные формы

    $.ajax({
        url: $(this).attr('action'), // URL, на который нужно отправить форму
        type: 'POST',
        data: data,
        success: function(response) {
            // обрабатываем успешный ответ от сервера
            if (response.success) {
                alert('Регистрация успешна!');
            } else {
                alert('Произошла ошибка при регистрации. Пожалуйста, попробуйте снова.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // обрабатываем ошибку на стороне сервера или проблемы с соединением
            alert('Произошла ошибка: ' + textStatus);
        }
    });
});
