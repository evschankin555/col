$('form#login-form').on('beforeSubmit', function(e) {
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                location.reload();
            } else {
                $("#login-modal .error-text").html(response.error).show();
            }
        },
        error: function () {
            alert("Something went wrong. Please try again.");
        }
    });
    return false;
});
window.addEventListener('load', function() {
    document.getElementById('forgot').addEventListener('click', function(e) {
        e.preventDefault();

        // Закрыть модальное окно login-modal
        var loginModalEl = document.getElementById('login-modal');
        var loginModal = new bootstrap.Modal(loginModalEl);
        loginModal.hide();

        // Показать модальное окно restore-modal
        var restoreModalEl = document.getElementById('restore-modal');
        var restoreModal = new bootstrap.Modal(restoreModalEl);
        restoreModal.show();
    });
});