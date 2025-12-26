// Initialize sidenav toggle
document.addEventListener('DOMContentLoaded', function () {
    const iconNavbarSidenav = document.getElementById('iconNavbarSidenav');
    const iconSidenav = document.getElementById('iconSidenav');
    const body = document.body;

    const className = 'g-sidenav-pinned';

    if (iconNavbarSidenav) {
        iconNavbarSidenav.addEventListener('click', toggleSidenav);
    }

    if (iconSidenav) {
        iconSidenav.addEventListener('click', toggleSidenav);
    }

    function toggleSidenav() {
        if (body.classList.contains(className)) {
            body.classList.remove(className);
            iconSidenav.classList.add('d-none');
        } else {
            body.classList.add(className);
            iconSidenav.classList.remove('d-none');
        }
    }
});