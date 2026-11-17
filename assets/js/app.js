/**
 * ExpenseTracker — small interactive helpers.
 */
document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('sidebar-overlay');

    if (menuBtn) {
        menuBtn.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
    }
    if (overlay) {
        overlay.addEventListener('click', () => document.body.classList.remove('sidebar-open'));
    }

    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 400);
        }, 3500);
    });
});
