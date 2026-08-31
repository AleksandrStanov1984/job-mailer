let toastTimer = null;

export function showToast(message, type = 'success') {
    const toast = document.getElementById('app-toast');
    const messageElement = document.getElementById('app-toast-message');

    if (!toast || !messageElement) {
        return;
    }

    if (toastTimer) {
        clearTimeout(toastTimer);
    }

    messageElement.textContent = message;

    toast.classList.remove(
        'app-toast-success',
        'app-toast-error',
        'app-toast-warning'
    );

    toast.classList.add(`app-toast-${type}`);
    toast.classList.add('is-visible');

    toastTimer = setTimeout(() => {
        toast.classList.remove('is-visible');
    }, 4000);
}
