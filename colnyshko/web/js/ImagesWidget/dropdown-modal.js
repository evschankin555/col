$(document).ready(function(){
    $(".dropdown-item").on("click", function (event) {
        event.preventDefault();
        let title = $(this).text();
        $("#modal-title").text(title);
        $("#modal-content").text(`Здесь будет содержимое для ${title}`);
        $('#myModal').modal('show');
    });


// Handle image click
    $(".image-modal").on("click", function (event) {
        event.preventDefault();
        let src = $(this).data('src');
        $("#media-modal-title").text($(this).attr('alt'));
        $("#media-modal-content").html(`<img src="${src}" style="max-width: 100%; max-height: 100%;">`);
        $('#mediaModal').modal('show');
    });

// Handle video click
    $(".video-modal").on("click", function (event) {
        event.preventDefault();
        let src = $(this).data('src');
        $("#media-modal-title").text($(this).attr('alt'));
        $("#media-modal-content").html(`<video src="${src}" style="max-width: 100%; max-height: 100%;" autoplay loop muted playsinline></video>`);
        $('#mediaModal').modal('show');
    });
    $('.modal').on('hidden.bs.modal', function (e) {
        $('.nav-link[data-bs-toggle="dropdown"]').addClass('show');
        $(document.activeElement).focus();
    });
    $('.dropdown').on('hide.bs.dropdown', function (e) {
        $('.nav-link[data-bs-toggle="dropdown"]').addClass('show');
    });
});