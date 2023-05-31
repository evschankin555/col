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
        let alt = $img.attr('alt');
        let href = $img.data('href');
        let isModal = false;
        if ($(this).hasClass('html')) {
            isModal = true;
            content = `<p>Код открытки для вставки в блог (HTML)</p>
                        <textarea class="form-control" id="exampleTextarea" rows="6" 
                         onclick="this.select();">&lt;a href='${href}' target="_blank"&gt;&lt;video autoplay loop muted playsinline src="${src}" alt="${alt}"/&gt;&lt;/video&gt;</textarea>`;
        } else if ($(this).hasClass('bb')) {
            isModal = true;
            content = `<p>Код открытки для вставки в форум (BB-Code)</p>
                        <textarea class="form-control" id="exampleTextarea" rows="3" 
                        onclick="this.select();">
                        [URL='${href}'][IMG]${src}[/IMG][/URL]</textarea>`;
        } else if ($(this).hasClass('link')) {
            isModal = true;
            content = `<p>Ссылка на открытку</p>
                        <textarea class="form-control" id="exampleTextarea" rows="2" 
                        onclick="this.select();">${href}</textarea>`;
        } else {
            content = `Здесь будет содержимое для ${title}`;
        }

        if(isModal){
            $("#modal-title").text(title);
            $("#modal-content").html(content);
            $('#myModal').modal('show');
        }
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
            let $img = $(this).closest('.card').find('.image-modal, .video-modal');
            let href = $img.data('href');
            console.log(id);
            window.ok_class(document,"ok_shareWidget_"+id, href,
                '{"sz":30,"st":"straight","ck":4,"bgclr":"ED8207","txclr":"ffffff"}',"","","");
        });
    });
});