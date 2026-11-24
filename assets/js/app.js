document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            const message = element.dataset.confirm || 'Apakah Anda yakin?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-bs-dismiss="alert"]').forEach((button) => {
        button.setAttribute('aria-label', 'Tutup notifikasi');
    });
});
