export function initTableFilter() {
    const filterButtons =
        document.querySelectorAll('[data-status-filter]');

    if (!filterButtons.length) {
        return;
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(item => {
                item.classList.remove('is-active');
            });

            button.classList.add('is-active');

            applyCurrentTableFilter();
        });
    });
}


export function applyCurrentTableFilter() {
    const tableBody =
        document.getElementById('recipients-table-body');

    if (!tableBody) {
        return;
    }

    const activeButton =
        document.querySelector(
            '[data-status-filter].is-active'
        );

    const filter =
        activeButton?.dataset.statusFilter ?? 'all';

    const rows =
        tableBody.querySelectorAll('[data-status]');

    rows.forEach(row => {
        const status = row.dataset.status;

        row.hidden = !matchesFilter(
            status,
            filter
        );
    });
}


function matchesFilter(status, filter) {
    if (filter === 'all') {
        return true;
    }

    if (filter === 'sent') {
        return status === 'sent';
    }

    if (filter === 'failed') {
        return status === 'failed';
    }

    if (filter === 'pending') {
        return status === 'pending';
    }

    if (filter === 'skipped') {
        return (
            status === 'skipped_recently_sent' ||
            status === 'duplicate_in_file'
        );
    }

    return true;
}
