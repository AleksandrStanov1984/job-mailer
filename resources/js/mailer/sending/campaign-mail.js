import { showToast } from '../../shared/toast';

import {
    getRecipients,
    replaceRecipients,
    updateRecipient,
} from '../recipients/recipients';

import {
    getSelectedAttachments,
} from '../files/files';


export function initCampaignMail() {
    const button =
        document.getElementById(
            'start-mailing-button'
        );

    if (!button) {
        return;
    }

    button.addEventListener(
        'click',
        async () => {
            await startCampaign(button);
        }
    );
}


async function startCampaign(button) {
    const recipients =
        getRecipients();

    const subjectInput =
        document.getElementById(
            'subject_template'
        );

    const messageInput =
        document.getElementById(
            'message'
        );

    const templateInput =
        document.getElementById(
            'template_file'
        );

    const duplicateProtection =
        document.getElementById(
            'duplicate_protection'
        );

    const duplicateDays =
        document.getElementById(
            'duplicate_days'
        );

    const delaySeconds =
        document.getElementById(
            'delay_seconds'
        );


    /*
    |--------------------------------------------------------------------------
    | Проверяем форму
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Отправляем исходные данные кампании серверу
    |--------------------------------------------------------------------------
    |
    | Важно:
    | JSON формируем из загруженных получателей.
    | Сервер НЕ доверяет готовым JS-статусам и сам повторно
    | определяет дубликаты и recent-send protection.
    |
    */

    const formData =
        new FormData();

    formData.append(
        'recipients_json',
        JSON.stringify(recipients)
    );

    formData.append(
        'subject_template',
        subjectInput.value
    );

    formData.append(
        'message_template',
        messageInput.value
    );

    const templateFile =
        templateInput?.files?.[0];

    if (templateFile) {
        formData.append(
            'template_original_name',
            templateFile.name
        );
    }

    formData.append(
        'duplicate_protection_enabled',
        duplicateProtection?.checked
            ? '1'
            : '0'
    );

    formData.append(
        'duplicate_protection_days',
        duplicateDays?.value || '7'
    );

    formData.append(
        'delay_seconds',
        delaySeconds?.value || '0'
    );


    /*
    |--------------------------------------------------------------------------
    | Все накопленные вложения
    |--------------------------------------------------------------------------
    */

    getSelectedAttachments()
        .forEach(file => {
            formData.append(
                'attachments[]',
                file
            );
        });


    setButtonRunning(
        button,
        true
    );


    try {
        /*
        |--------------------------------------------------------------------------
        | 1. Создаём Campaign
        |--------------------------------------------------------------------------
        */

        const response =
            await fetch(
                '/campaigns',
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
            await parseResponse(response);

        if (!response.ok) {
            throw new Error(
                getErrorMessage(data)
            );
        }

        const campaign =
            data.campaign;

        replaceRecipients(
            campaign.recipients
        );


        /*
        |--------------------------------------------------------------------------
        | 2. Последовательная отправка
        |--------------------------------------------------------------------------
        */

        const pendingRecipients =
            campaign.recipients.filter(
                recipient =>
                    recipient.status ===
                    'pending'
            );

        for (
            let index = 0;
            index < pendingRecipients.length;
            index++
        ) {
            const recipient =
                pendingRecipients[index];


            /*
            |--------------------------------------------------------------------------
            | Показываем "Отправляется"
            |--------------------------------------------------------------------------
            */

            updateRecipient({
                ...recipient,
                status: 'sending',
            });


            /*
            |--------------------------------------------------------------------------
            | Реальная SMTP-отправка
            |--------------------------------------------------------------------------
            */

            try {
                const sendResponse =
                    await fetch(
                        `/campaigns/${campaign.id}/recipients/${recipient.id}/send`,
                        {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN':
                                    getCsrfToken(),

                                'Accept':
                                    'application/json',
                            },
                        }
                    );

                const sendData =
                    await parseResponse(
                        sendResponse
                    );

                if (!sendResponse.ok) {
                    throw new Error(
                        getErrorMessage(
                            sendData
                        )
                    );
                }

                updateRecipient(
                    sendData.recipient
                );

            } catch (error) {
                /*
                 * Если сервер успел сохранить failed,
                 * но сам HTTP-запрос оборвался, точный
                 * статус останется в SQLite.
                 *
                 * В UI показываем ошибку запроса.
                 */
                updateRecipient({
                    ...recipient,
                    status: 'failed',
                    error_message:
                        error.message ??
                        'Ошибка отправки.',
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Пауза только МЕЖДУ письмами
            |--------------------------------------------------------------------------
            */

            const isLast =
                index ===
                pendingRecipients.length - 1;

            if (
                !isLast &&
                Number(
                    campaign.delay_seconds
                ) > 0
            ) {
                await wait(
                    Number(
                        campaign.delay_seconds
                    ) * 1000
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Готово
        |--------------------------------------------------------------------------
        */

        const sentCount =
            getRecipients().filter(
                recipient =>
                    recipient.status ===
                    'sent'
            ).length;

        const failedCount =
            getRecipients().filter(
                recipient =>
                    recipient.status ===
                    'failed'
            ).length;

        const skippedCount =
            getRecipients().filter(
                recipient =>
                    recipient.status ===
                    'skipped_recently_sent' ||
                    recipient.status ===
                    'duplicate_in_file'
            ).length;

        showToast(
            `Рассылка завершена. Отправлено: ${sentCount}, ошибок: ${failedCount}, пропущено: ${skippedCount}.`
        );

    } catch (error) {
        showToast(
            error.message ??
            'Не удалось запустить рассылку.',
            'error'
        );

    } finally {
        setButtonRunning(
            button,
            false
        );
    }
}


function setButtonRunning(
    button,
    running
) {
    if (running) {
        button.disabled = true;

        button.dataset.originalText =
            button.textContent;

        button.textContent =
            'Рассылка выполняется...';

        return;
    }

    button.disabled = false;

    button.textContent =
        button.dataset.originalText ??
        'Начать рассылку';
}


function wait(milliseconds) {
    return new Promise(resolve => {
        setTimeout(
            resolve,
            milliseconds
        );
    });
}


async function parseResponse(response) {
    const contentType =
        response.headers.get(
            'content-type'
        ) ?? '';

    if (
        contentType.includes(
            'application/json'
        )
    ) {
        return await response.json();
    }

    const text =
        await response.text();

    return {
        message:
            text ||
            `HTTP ${response.status}`,
    };
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
        'Ошибка выполнения запроса.'
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
