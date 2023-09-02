class UserPage {
    /**
     * Конструктор класса UserPage.
     * Инициализирует объект и вызывает метод init для привязки событий.
     */
    constructor() {
        this.init();
    }
    /**
     * Инициализация класса.
     * Вызывает метод bindEvents для привязки всех необходимых событий.
     */
    init() {
        this.bindEvents();
    }
    /**
     * Привязка событий к элементам страницы.
     * Устанавливает обработчики событий для подписки, отписки, создания и удаления коллекций.
     */
    bindEvents() {
        // События для подписки и отписки
        $(document).on('click', '#subscribe-btn', this.subscribe.bind(this));
        $(document).on('click', '#unsubscribe-btn', this.unsubscribe.bind(this));

        // События для создания и удаления коллекций
        let createCollectionButton = document.getElementById('createCollectionButton');
        if (createCollectionButton !== null) {
            createCollectionButton.addEventListener('click', this.showCreateCollectionModal.bind(this));
        }
        $('#create-collection-btn').click(this.createCollection.bind(this));
        $('#deleteCollectionButton').click(this.showDeleteCollectionModal.bind(this));
        $('#confirm-delete-btn').click(this.deleteCollection.bind(this));
    }
    /**
     * Подписка на пользователя.
     * Отправляет AJAX-запрос для подписки на пользователя и обновляет интерфейс в случае успеха.
     *
     * @param {Event} event - Событие клика.
     */
    subscribe(event) {
        const button = $(event.target);
        $.ajax({
            url: '/user/subscribe?username=' + encodeURIComponent(button.data('username')),
            type: 'POST',
            success: (data) => {
                if (data.success) {
                    button.text('Отписаться')
                        .attr('id', 'unsubscribe-btn')
                        .toggleClass('btn-secondary btn-danger');
                    $('.subscribersCount').text(data.subscribersCount);
                    $('.subscriptionsCount').text(data.subscriptionsCount);
                    this.showToast('Вы подписаны.', 'success', 1500);
                } else {
                    this.showToast(data.message, 'danger', 15000);
                }
            }
        });
        return false;
    }
    /**
     * Отписка от пользователя.
     * Отправляет AJAX-запрос для отписки от пользователя и обновляет интерфейс в случае успеха.
     *
     * @param {Event} event - Событие клика.
     */
    unsubscribe(event) {
        const button = $(event.target);
        $.ajax({
            url: '/user/unsubscribe?username=' + encodeURIComponent(button.data('username')),
            type: 'POST',
            success: (data) => {
                if (data.success) {
                    button.text('Подписаться')
                        .attr('id', 'subscribe-btn')
                        .toggleClass('btn-danger btn-secondary');
                    $('.subscribersCount').text(data.subscribersCount);
                    $('.subscriptionsCount').text(data.subscriptionsCount);
                    this.showToast('Вы отписаны.', 'success', 2000);
                } else {
                    this.showToast(data.message, 'danger', 15000);
                }
            }
        });
        return false;
    }
    /**
     * Показать модальное окно для создания коллекции.
     * Инициализирует и отображает модальное окно для создания новой коллекции.
     */
    showCreateCollectionModal() {
        const myModal = new bootstrap.Modal(document.getElementById('createCollection'), {});
        myModal.show();
    }
    /**
     * Создание новой коллекции.
     * Считывает имя новой коллекции и вызывает функцию для ее создания.
     */
    createCollection() {
        const collectionName = $('#new-collection-name').val();

        this.createAndAddCollection(collectionName, function(success) {
            if (success) {
                $('#new-collection-name').val('');
                $('#createCollection').modal('hide');
            }
        });
    }
    /**
     * Показать модальное окно для удаления коллекции.
     * Инициализирует и отображает модальное окно для удаления выбранной коллекции.
     *
     * @param {Event} event - Событие клика.
     */
    showDeleteCollectionModal(event) {
        const button = $(event.target);
        const collectionId = button.data('collection-id');
        const username = button.data('username');
        $('#delete-collection-id').val(collectionId);
        $('#delete-collection-username').val(username);

        const myModal = new bootstrap.Modal(document.getElementById('deleteCollection'), {});
        myModal.show();
    }
    /**
     * Удаление коллекции.
     * Отправляет AJAX-запрос для удаления коллекции и обновляет интерфейс в случае успеха.
     */
    deleteCollection() {
        const collectionId = $('#delete-collection-id').val();
        const username = $('#delete-collection-username').val();

        $.ajax({
            url: '/user/delete-collection',
            type: 'POST',
            data: { id: collectionId },
            success: (data) => {
                if (data.success) {
                    this.showToast(data.message, 'success', 1500);
                    // Удалите коллекцию из списка
                    $('#' + collectionId).remove();
                    setTimeout(function() {
                        window.location.href = "/" + username;
                    }, 1000);
                } else {
                    this.showToast(data.message, 'danger', 15000);
                }
            }
        });

        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteCollection'));
        deleteModal.hide();
    }
    /**
     * Показать уведомление.
     * Вызывает функцию showToast для отображения уведомлений на странице.
     *
     * @param {string} message - Сообщение уведомления.
     * @param {string} type - Тип уведомления (success, danger, etc.)
     * @param {number} duration - Продолжительность отображения уведомления (в миллисекундах).
     */
    showToast(message, type, duration) {
        showToast(message, type, duration);
    }
}

// Инициализируем новый класс после загрузки DOM
$(document).ready(function() {
    new UserPage();
});
window.isCanceled = false;