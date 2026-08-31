import { getRecipients } from '../recipients/recipients';
import { renderTemplate } from '../template/template-renderer';

export function initPreview() {
    const message =
        document.getElementById('message');

    const subject =
        document.getElementById('subject_template');

    const recipientsInput =
        document.getElementById('recipients');

    const templateInput =
        document.getElementById('template_file');

    if (message) {
        message.addEventListener(
            'input',
            updatePreview
        );
    }

    if (subject) {
        subject.addEventListener(
            'input',
            updatePreview
        );
    }

    if (recipientsInput) {
        recipientsInput.addEventListener(
            'change',
            () => {
                /*
                 * recipients.js делает fetch асинхронно.
                 * Сам updatePreview вызовем оттуда после
                 * успешной загрузки JSON.
                 */
            }
        );
    }

    if (templateInput) {
        templateInput.addEventListener(
            'change',
            () => {
                /*
                 * files.js сначала прочитает TXT.
                 * После этого preview обновится через событие,
                 * которое добавим ниже.
                 */
            }
        );
    }

    document.addEventListener(
        'mailer:recipients-loaded',
        updatePreview
    );

    document.addEventListener(
        'mailer:template-loaded',
        updatePreview
    );
}


export function updatePreview() {
    const recipients = getRecipients();

    const message =
        document.getElementById('message');

    const subject =
        document.getElementById('subject_template');

    const empty =
        document.getElementById('preview-empty');

    const content =
        document.getElementById('preview-content');

    if (
        !recipients.length ||
        !message ||
        !message.value.trim()
    ) {
        showEmptyPreview(
            empty,
            content
        );

        return;
    }

    const recipient =
        recipients.find(
            item => item.status !== 'duplicate_in_file'
        ) ?? recipients[0];

    setText(
        'preview-company',
        recipient.company || '—'
    );

    setText(
        'preview-email',
        recipient.email || '—'
    );

    setText(
        'preview-vacancy',
        recipient.vacancy || '—'
    );

    setText(
        'preview-contact',
        recipient.contact_name || 'Не указано'
    );

    setText(
        'preview-subject',
        renderTemplate(
            subject?.value ?? '',
            recipient
        )
    );

    setText(
        'preview-message',
        renderTemplate(
            message.value,
            recipient
        )
    );

    if (empty) {
        empty.hidden = true;
    }

    if (content) {
        content.hidden = false;
    }
}


function showEmptyPreview(empty, content) {
    if (empty) {
        empty.hidden = false;
    }

    if (content) {
        content.hidden = true;
    }
}


function setText(id, value) {
    const element =
        document.getElementById(id);

    if (element) {
        element.textContent = value;
    }
}
