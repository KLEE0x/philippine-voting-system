document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.confirm-form');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to continue?')) {
                e.preventDefault();
            }
        });
    });

    const toggle = document.querySelector('[data-toggle-sidebar]');
    if (toggle) {
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
    }

    document.addEventListener('click', function (e) {
        if (document.body.classList.contains('sidebar-open') && !e.target.closest('.sidebar') && !e.target.closest('[data-toggle-sidebar]')) {
            document.body.classList.remove('sidebar-open');
        }
    });

    const ballotSections = document.querySelectorAll('[data-max-selection]');
    ballotSections.forEach(function (section) {
        const max = parseInt(section.getAttribute('data-max-selection'), 10);
        const warning = section.querySelector('.selection-warning');
        const inputs = section.querySelectorAll('input[type="checkbox"]');
        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                const checked = section.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > max) {
                    input.checked = false;
                    if (warning) {
                        warning.style.display = 'block';
                        setTimeout(function () { warning.style.display = 'none'; }, 2200);
                    }
                }
            });
        });
    });
});
