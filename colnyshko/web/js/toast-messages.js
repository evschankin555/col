function showToast(message, messageType, delay) {
    var title;
    switch(messageType) {
        case 'success':
            title = "Успешно!";
            break;
        case 'warning':
            title = "Внимание!";
            break;
        case 'danger':
            title = "Ошибка!";
            break;
        default:
            title = "Уведомление";
    }

    var toastHTML = `
    <div id="dynamic-toast" class="toast fade translate-right alert-${messageType}" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 120px; right: 20px; min-width: 200px; transition: transform 0.6s linear, opacity 0.6s linear; padding-left: 8px;">
        <div class="toast-header">
            <strong class="me-auto">${title}</strong>
            <small>только что</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    </div>`;

    // Если предыдущий тост существует, удалите его
    if (document.getElementById('dynamic-toast')) {
        document.getElementById('dynamic-toast').remove();
    }

    // Добавьте новый тост в body
    document.body.insertAdjacentHTML('beforeend', toastHTML);

    // Показать тост
    var toastEl = document.getElementById('dynamic-toast');
    var toast = new bootstrap.Toast(toastEl, { delay: delay });  // передаем delay как параметр
    toast.show();
}
document.addEventListener("DOMContentLoaded", function() {
    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    for (const tooltip of tooltipElements) {
        new bootstrap.Tooltip(tooltip);
    }

    // Initialize popovers
    const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
    let activePopover = null;  // Store reference to currently shown popover

    for (const popover of popoverElements) {
        const bsPopover = new bootstrap.Popover(popover, {
            trigger: 'manual' // Important: set trigger to manual
        });

        // Show popover on hover
        popover.addEventListener('mouseenter', function() {
            if (activePopover) {
                activePopover.hide();
            }
            bsPopover.show();
            activePopover = bsPopover;
        });

        // Hide the popover when the same element is clicked
        popover.addEventListener('click', function() {
            if (activePopover) {
                activePopover.hide();
                activePopover = null;
            }
        });
    }

    // Hide the popover when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[data-bs-toggle="popover"]') && activePopover) {
            activePopover.hide();
            activePopover = null;
        }
    });
});
