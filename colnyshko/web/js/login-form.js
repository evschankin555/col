
function isAgreement(form){
    let cb = $(`form[name=${form}]`).find(`.agree-checkbox`);
    if(cb.length) return cb.is(':checked');
    return true;
}

function validateForm(form, fields) {
    var inputs = $(`form[name=${form}]`).find('.myform__text');
    var textarea = $(`form[name=${form}]`).find('.myform__textarea');

    if(fields){
        inputs = fields.inputs;
        textarea = fields.textarea;
        console.log(inputs);
    }

    var isValid = true;
    inputs.each(function(){
        var form_row = $(this).closest('.form-row');
        $(this).trigger('blur');
        if(form_row.hasClass('not-valid') || form_row.hasClass('not-empty')){
            isValid = false;
        }
    })
    textarea.each(function(){
        var form_row = $(this).closest('.form-row');
        $(this).trigger('blur');
        if(form_row.hasClass('not-valid') || form_row.hasClass('not-empty')){
            isValid = false;
        }
    })

    if(!isAgreement(form)){
        isValid = false;
    }

    if(!isValid) {
        $(`form[name=${form}]`).addClass('not-valid')
    } else {
        $(`form[name=${form}]`).removeClass('not-valid')
    }

    return isValid;
}

$("#login-button").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (validateForm('loginForm')) {
        var data = {
            email: $("#login-form #user-email").val(),
            password: $("#login-form #user-password").val(),
        };
        $.post("/auth", data, function (response){
            if (response.success) {
                document.location.href = document.location.href;
            }else{
                let notification = $("#login .error-text");
                notification.show().html('Не верный емейл или пароль.');
            }
        });
    }
});

$("#logout").on("click", function (e) {
    console.log('logout');
    $.get("/logout", function (response){
        if (response.success) {
            window.location.href = window.location.href;
        } else {
            alert('Ошибка выхода');
        }
    });
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