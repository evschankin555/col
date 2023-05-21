window.ok_class = function (d, id, did, st, title, description, image) {
    OK.CONNECT.insertShareWidget(id,did,st, title, description, image);
};

$(document).ready(function(){
    $(".dropdown-item").on("click", function (event) {
        //event.preventDefault();

        let title = $(this).text();
        let content;
        let $img = $(this).closest('.card').find('.image-modal, .video-modal');

        let src = $img.data('src');
        let alt = $img.data('alt');
        let href = document.location.href;
        if ($(this).hasClass('html')) {
            content = `<p>Код открытки для вставки в блог (HTML)</p>
                        <textarea class="form-control" id="exampleTextarea" rows="6" 
                         onclick="this.select();">&lt;a href='${href}' target="_blank"&gt;&lt;video autoplay loop muted playsinline src="${src}" alt="${alt}"/&gt;&lt;/video&gt;</textarea>`;
        } else if ($(this).hasClass('bb')) {
            content = `<p>Код открытки для вставки в форум (BB-Code)</p>
                        <textarea class="form-control" id="exampleTextarea" rows="3" 
                        onclick="this.select();">
                        [URL='${href}'][IMG]${src}[/IMG][/URL]</textarea>`;
        } else if ($(this).hasClass('link')) {
            content = `<p>Ссылка на открытку</p>
                        <textarea class="form-control" id="exampleTextarea" rows="1" 
                        onclick="this.select();">${href}</textarea>`;
        } else {
            content = `Здесь будет содержимое для ${title}`;
        }

        $("#modal-title").text(title);
        $("#modal-content").html(content);
        $('#myModal').modal('show');
    });

    $('.modal').on('hidden.bs.modal', function (e) {
        $('.nav-link[data-bs-toggle="dropdown"]').addClass('show');
        $(document.activeElement).focus();
    });

    $('.dropdown').on('hide.bs.dropdown', function (e) {
        $('.nav-link[data-bs-toggle="dropdown"]').addClass('show');
    });

    $(".btn.btn-primary.images-copy").on("click", function(event) {
        event.preventDefault();
        let content = document.querySelector('#exampleTextarea');
        content.select();
        document.execCommand("copy");

        var toast = new bootstrap.Toast(document.getElementById('copy-toast'));
        toast.show();
        $("#myModal button.btn-secondary").trigger("click")
    });
    window.addEventListener('load', function () {
        console.log('ok_shareWidget');
        console.log($(".cards-images .card-body"));
        $(".cards-images .card-body").each(function () {
            let id = $(this).data('id');
            console.log(id);
            window.ok_class(document,"ok_shareWidget_"+id,document.URL,
                '{"sz":30,"st":"straight","ck":4,"bgclr":"ED8207","txclr":"ffffff"}',"","","");
        });
    });
});