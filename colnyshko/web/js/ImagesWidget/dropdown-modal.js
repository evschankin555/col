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
                        <textarea class="form-control" id="exampleTextarea" rows="8" 
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
// Initialize popovers
    const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
    console.log(popoverElements);
    for (const popover of popoverElements) {
        new bootstrap.Popover(popover); // eslint-disable-line no-new
    }
    function getQueryParameters(url) {
        var queryString = url.split('?')[1] || '';
        var pairs = queryString.split('&');
        var result = {};
        pairs.forEach(function(pair) {
            pair = pair.split('=');
            if (pair[0]) {
                result[pair[0]] = decodeURIComponent(pair[1] || '');
            }
        });
        return result;
    }

    function updateQueryStringParameter(url, key, value, defaultValue) {
        var baseUrl = url.split('?')[0];
        var params = getQueryParameters(url);

        if (value == defaultValue) {
            delete params[key];
        } else {
            params[key] = value;
        }

        var newQueryParams = Object.keys(params).map(function(key) {
            return encodeURIComponent(key) + "=" + encodeURIComponent(params[key]);
        }).join('&');

        return baseUrl + (newQueryParams ? '?' + newQueryParams : '');
    }

// Теперь функцию можно использовать следующим образом:

// jQuery('.display-buttons .dropdown-item').click(function() {
//    ...
//    var newUrl = updateQueryStringParameter(window.location.href, 'display', value, 'all');
//    ...
// });

// jQuery('.sort-buttons .dropdown-item').click(function() {
//    ...
//    var newUrl = updateQueryStringParameter(window.location.href, 'sort', value, 'new');
//    ...
// });




    jQuery('.display-buttons .dropdown-item').click(function() {
        var value = jQuery(this).data('value');
        var text = jQuery(this).text();
        var buttonClass = 'btn-secondary';

        if (value === 'animations') {
            buttonClass = 'btn-primary';
        } else if (value === 'static') {
            buttonClass = 'btn-danger';
        }

        jQuery('#displayButton')
            .text('Отобразить: ' + text)
            .removeClass('btn-secondary btn-primary btn-danger')
            .addClass(buttonClass);

        jQuery('#displayDropdownButton')
            .removeClass('btn-secondary btn-primary btn-danger')
            .addClass(buttonClass);

        // Обновляем URL, добавляя параметр `display`, если его значение не равно 'all'
        var newUrl = updateQueryStringParameter(window.location.href, 'display', value, 'all');
        window.location.href = newUrl;
    });

    jQuery('.sort-buttons .dropdown-item').click(function() {
        var value = jQuery(this).data('value');
        var text = jQuery(this).text();

        jQuery('#sortButton')
            .text('Сортировать: ' + text);

        jQuery('#sortDropdownButton');

        // Обновляем URL, добавляя параметр `sort`, если его значение не равно 'new'
        var newUrl = updateQueryStringParameter(window.location.href, 'sort', value, 'new');
        window.location.href = newUrl;
    });



});