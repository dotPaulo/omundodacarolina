document.addEventListener('DOMContentLoaded', function () {
    const logoutMessage = document.getElementById('logout-message');

    function showMessage() {
        logoutMessage.classList.add('show');
        setTimeout(hideMessage, 5000);
    }

    function hideMessage() {
        logoutMessage.classList.add('hide');
        logoutMessage.addEventListener('transitionend', function () {
            logoutMessage.classList.remove('show', 'hide');
        }, { once: true });
    }

    if (showLogoutMessage) {
        showMessage();
        const url = new URL(window.location);
        url.searchParams.delete('logout');
        window.history.replaceState({}, '', url);
    }
});