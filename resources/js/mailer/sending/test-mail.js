import { showToast } from '../../shared/toast';

import {
    getRecipients
} from '../recipients/recipients';

import {
    renderTemplate
} from '../template/template-renderer';

import {
    getSelectedAttachments
} from '../files/files';


export function initTestMail() {
    const button =
        document.getElementById(
            'send-test-button'
        );

    if (!button) {
        return;
    }

    button.addEventListener(
        'click',
        async () => {
            await sendTestMail(button);
        }
    );
}


async function sendTestMail(button) {
    const testEmail =
        document.getElementById(
            'test_email'
        );

    const subjectInput =
        document.getElementById(
            'subject_template'
        );

    const messageInput =
        document.getElementById(
            'message'
        );

    const recipients =
        getRecipients();


    /*
    |--------------------------------------------------------------------------
    | Проверки
    |--------------------------------------------------------------------------
    */

    if (!testEmail?.value.trim()) {
        showToast(
            'Введите email для тестовой отправки.',
            'error'
        );

        return;
    }


    if (!recipients.length) {
        showToast(
            'Сначала загрузите JSON с получателями.',
            'error'
        );

        return;
    }


    if (!subjectInput?.value.trim()) {
        showToast(
            'Укажите тему письма.',
            'error'
        );

        return;
    }


    if (!messageInput?.value.trim()) {
        showToast(
            'Сначала загрузите или введите текст письма.',
            'error'
        );

        return;
    }


    const recipient =
        recipients.find(
            item =>
                item.status !==
                'duplicate_in_file'
        );


    if (!recipient) {
        showToast(
            'В JSON нет получателя для тестового письма.',
            'error'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Формируем реальное письмо
    |--------------------------------------------------------------------------
    */

    const subject =
        renderTemplate(
            subjectInput.value,
            recipient
        );


    const message =
        renderTemplate(
            messageInput.value,
            recipient
        );


    /*
    |--------------------------------------------------------------------------
    | FormData
    |--------------------------------------------------------------------------
    */

    const formData =
        new FormData();


    formData.append(
        'email',
        testEmail.value.trim()
    );


    formData.append(
        'subject',
        subject
    );


    formData.append(
        'message',
        message
    );


    /*
    |--------------------------------------------------------------------------
    | ВСЕ накопленные вложения
    |--------------------------------------------------------------------------
    */

    const attachments =
        getSelectedAttachments();


    attachments.forEach(file => {
        formData.append(
            'attachments[]',
            file
        );
    });


    /*
    |--------------------------------------------------------------------------
    | Отправка
    |--------------------------------------------------------------------------
    */

    setButtonLoading(
        button,
        true
    );


    try {
        const response =
            await fetch(
                '/mail/test',
                {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            getCsrfToken(),

                        'Accept':
                            'application/json',
                    },

                    body: formData,
                }
            );


        const data =
            await response.json();


        if (!response.ok) {
            throw new Error(
                getErrorMessage(data)
            );
        }


        showToast(
            data.message ??
            `Тестовое письмо отправлено. Вложений: ${attachments.length}`
        );

    } catch (error) {
        showToast(
            error.message ??
            'Не удалось отправить тестовое письмо.',
            'error'
        );

    } finally {
        setButtonLoading(
            button,
            false
        );
    }
}


function setButtonLoading(
    button,
    loading
) {
    if (loading) {
        button.disabled = true;

        button.dataset.originalText =
            button.textContent;

        button.textContent =
            'Отправка...';

        return;
    }


    button.disabled = false;

    button.textContent =
        button.dataset.originalText ??
        'Отправить тестовое письмо';
}


function getErrorMessage(data) {
    if (data?.errors) {
        const firstError =
            Object.values(
                data.errors
            )
                .flat()
                .find(Boolean);

        if (firstError) {
            return firstError;
        }
    }


    return (
        data?.message ??
        'Не удалось отправить тестовое письмо.'
    );
}


function getCsrfToken() {
    return document
        .querySelector(
            'meta[name="csrf-token"]'
        )
        ?.getAttribute(
            'content'
        ) ?? '';
}
