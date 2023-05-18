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
$('#forgot').on('click', function(e) {
    e.preventDefault();
    $('#login-modal').modal('hide');
    $('#restore-modal').modal('show');
});