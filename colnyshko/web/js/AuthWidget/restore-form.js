$('form#restore-form').on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                location.reload();
            } else {
                $("#restore-modal .error-text").html(response.error).show();
            }
        },
        error: function () {
            alert("Something went wrong. Please try again.");
        }
    });
    return false;
});