import { showToast } from '../../shared/toast';
import { applyCurrentTableFilter } from './table-filter';

let recipients = [];


export function initRecipients() {
    const input =
        document.getElementById('recipients');

    if (!input) {
        return;
    }

    input.addEventListener('change', async () => {
        const file = input.files[0];

        if (!file) {
            resetRecipients();
            return;
        }

        await loadRecipients(file);
    });
}


async function loadRecipients(file) {
    const formData = new FormData();

    formData.append('recipients', file);

    try {
        const response = await fetch('/recipients/preview', {
            method: 'POST',

            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },

            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ?? 'Не удалось обработать JSON.'
            );
        }

        recipients = data.recipients;

        updateRecipientsInfo(data);
        renderRecipients();
        updateStats();

        applyCurrentTableFilter();

        /*
        |--------------------------------------------------------------------------
        | Сообщаем другим модулям, что получатели готовы
        |--------------------------------------------------------------------------
        |
        | Это событие использует preview.js.
        |
        */
        document.dispatchEvent(
            new CustomEvent('mailer:recipients-loaded')
        );

        showToast(
            `Получатели загружены: ${data.valid_for_sending}`
        );

    } catch (error) {
        resetRecipients();

        showToast(
            error.message ?? 'Не удалось обработать JSON.',
            'error'
        );
    }
}


function renderRecipients() {
    const tbody =
        document.getElementById('recipients-table-body');

    if (!tbody) {
        return;
    }

    tbody.innerHTML = '';

    recipients.forEach((recipient, index) => {
        const row =
            document.createElement('tr');

        row.dataset.status =
            recipient.status;

        row.innerHTML = `
            <td>
                ${index + 1}
            </td>

            <td>
                ${escapeHtml(recipient.company ?? '—')}
            </td>

            <td>
                ${escapeHtml(recipient.email)}
            </td>

            <td>
                ${escapeHtml(recipient.vacancy ?? '—')}
            </td>

            <td>
                ${renderStatus(recipient.status)}
            </td>

            <td>
                ${renderResult(recipient)}
            </td>
        `;

        tbody.appendChild(row);
    });

    if (!recipients.length) {
        tbody.innerHTML = `
            <tr data-empty-row="true">
                <td colspan="6">
                    Получатели пока не загружены.
                </td>
            </tr>
        `;
    }
}


function updateStats() {
    const total =
        recipients.length;

    const sent =
        countStatus('sent');

    const failed =
        countStatus('failed');

    const skipped =
        recipients.filter(recipient => {
            return (
                recipient.status === 'skipped_recently_sent' ||
                recipient.status === 'duplicate_in_file'
            );
        }).length;

    const pending =
        countStatus('pending');

    setText('stat-all', total);
    setText('stat-sent', sent);
    setText('stat-failed', failed);
    setText('stat-skipped', skipped);
    setText('stat-pending', pending);
}


function updateRecipientsInfo(data) {
    const info =
        document.getElementById('recipients-info');

    if (!info) {
        return;
    }

    let text =
        `Найдено: ${data.count}. К отправке: ${data.valid_for_sending}.`;

    if (data.duplicates > 0) {
        text +=
            ` Дубликатов в файле: ${data.duplicates}.`;
    }

    info.textContent = text;
}


function resetRecipients() {
    recipients = [];

    const info =
        document.getElementById('recipients-info');

    const tbody =
        document.getElementById('recipients-table-body');

    if (info) {
        info.textContent =
            'Компания · Email · Вакансия · Контактное лицо';
    }

    if (tbody) {
        tbody.innerHTML = `
            <tr data-empty-row="true">
                <td colspan="6">
                    Получатели пока не загружены.
                </td>
            </tr>
        `;
    }

    updateStats();
    applyCurrentTableFilter();

    /*
    |--------------------------------------------------------------------------
    | Очищаем preview
    |--------------------------------------------------------------------------
    |
    | То же событие отправляем и при сбросе.
    | Preview увидит пустой массив и скроет письмо.
    |
    */
    document.dispatchEvent(
        new CustomEvent('mailer:recipients-loaded')
    );
}


function countStatus(status) {
    return recipients.filter(
        recipient => recipient.status === status
    ).length;
}


function renderStatus(status) {
    const statuses = {
        pending: {
            label: 'Ожидает',
            className: 'status-pending',
        },

        sending: {
            label: 'Отправляется',
            className: 'status-sending',
        },

        sent: {
            label: 'Отправлено',
            className: 'status-sent',
        },

        failed: {
            label: 'Ошибка',
            className: 'status-failed',
        },

        skipped_recently_sent: {
            label: 'Недавно отправляли',
            className: 'status-skipped',
        },

        duplicate_in_file: {
            label: 'Дубликат в файле',
            className: 'status-duplicate',
        },
    };

    const config =
        statuses[status] ?? {
            label: status,
            className: '',
        };

    return `
        <span class="status ${config.className}">
            ${escapeHtml(config.label)}
        </span>
    `;
}

function renderResult(recipient) {
    if (recipient.error_message) {
        return `
            <span title="${escapeHtml(recipient.error_message)}">
                ${escapeHtml(recipient.error_message)}
            </span>
        `;
    }

    const date =
        recipient.sent_at ??
        recipient.failed_at ??
        recipient.skipped_at;

    if (!date) {
        return '—';
    }

    const parsed =
        new Date(date);

    if (Number.isNaN(parsed.getTime())) {
        return escapeHtml(date);
    }

    return escapeHtml(
        parsed.toLocaleString('ru-RU')
    );
}

function setText(id, value) {
    const element =
        document.getElementById(id);

    if (element) {
        element.textContent = value;
    }
}


function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';
}


function escapeHtml(value) {
    const div =
        document.createElement('div');

    div.textContent =
        String(value);

    return div.innerHTML;
}


export function getRecipients() {
    return recipients;
}

export function replaceRecipients(newRecipients) {
    recipients = newRecipients;

    renderRecipients();
    updateStats();
    applyCurrentTableFilter();
}


export function updateRecipient(updatedRecipient) {
    const index = recipients.findIndex(
        recipient =>
            Number(recipient.id) ===
            Number(updatedRecipient.id)
    );

    if (index === -1) {
        return;
    }

    recipients[index] = {
        ...recipients[index],
        ...updatedRecipient,
    };

    renderRecipients();
    updateStats();
    applyCurrentTableFilter();
}
