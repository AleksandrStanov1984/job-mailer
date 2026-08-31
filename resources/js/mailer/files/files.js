let selectedAttachments = [];


export function initFileInputs() {
    const recipientsInput =
        document.getElementById('recipients');

    const templateInput =
        document.getElementById('template_file');

    const attachmentsInput =
        document.getElementById('attachments');

    const recipientsFileName =
        document.getElementById('recipients-file-name');

    const templateFileName =
        document.getElementById('template-file-name');

    const attachmentsFileName =
        document.getElementById('attachments-file-name');

    const attachmentsList =
        document.getElementById('attachments-list');

    const messageTextarea =
        document.getElementById('message');


    /*
    |--------------------------------------------------------------------------
    | JSON
    |--------------------------------------------------------------------------
    */

    if (
        recipientsInput &&
        recipientsFileName
    ) {
        recipientsInput.addEventListener(
            'change',
            () => {
                const file =
                    recipientsInput.files[0];

                recipientsFileName.textContent =
                    file
                        ? file.name
                        : 'Файл не выбран';
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TXT
    |--------------------------------------------------------------------------
    */

    if (
        templateInput &&
        templateFileName &&
        messageTextarea
    ) {
        templateInput.addEventListener(
            'change',
            async () => {
                const file =
                    templateInput.files[0];

                if (!file) {
                    templateFileName.textContent =
                        'Файл не выбран';

                    messageTextarea.value = '';

                    document.dispatchEvent(
                        new CustomEvent(
                            'mailer:template-loaded'
                        )
                    );

                    return;
                }

                templateFileName.textContent =
                    file.name;

                messageTextarea.value =
                    await file.text();

                document.dispatchEvent(
                    new CustomEvent(
                        'mailer:template-loaded'
                    )
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Вложения
    |--------------------------------------------------------------------------
    |
    | Каждый новый выбор ДОБАВЛЯЕТ файлы.
    | Уже выбранные файлы не исчезают.
    |
    */

    if (
        attachmentsInput &&
        attachmentsFileName &&
        attachmentsList
    ) {
        attachmentsInput.addEventListener(
            'change',
            () => {
                const newFiles =
                    Array.from(
                        attachmentsInput.files
                    );

                newFiles.forEach(file => {
                    if (!attachmentExists(file)) {
                        selectedAttachments.push(file);
                    }
                });

                renderAttachments(
                    attachmentsFileName,
                    attachmentsList
                );

                /*
                 * Очищаем browser input,
                 * но НЕ selectedAttachments.
                 *
                 * Поэтому следующий выбор файла
                 * добавится к существующим.
                 */
                attachmentsInput.value = '';
            }
        );
    }
}


function attachmentExists(file) {
    return selectedAttachments.some(
        existingFile => {
            return (
                existingFile.name === file.name &&
                existingFile.size === file.size &&
                existingFile.lastModified ===
                file.lastModified
            );
        }
    );
}


function renderAttachments(
    attachmentsFileName,
    attachmentsList
) {
    attachmentsList.innerHTML = '';

    if (!selectedAttachments.length) {
        attachmentsFileName.textContent =
            'Файлы не выбраны';

        return;
    }

    attachmentsFileName.textContent =
        `Выбрано файлов: ${selectedAttachments.length}`;

    selectedAttachments.forEach(
        (file, index) => {
            const item =
                document.createElement('div');

            item.className =
                'selected-file-item';

            const fileName =
                document.createElement('span');

            fileName.className =
                'selected-file-name';

            fileName.textContent =
                file.name;

            const removeButton =
                document.createElement('button');

            removeButton.type =
                'button';

            removeButton.className =
                'selected-file-remove';

            removeButton.textContent =
                '×';

            removeButton.title =
                'Удалить вложение';

            removeButton.addEventListener(
                'click',
                () => {
                    selectedAttachments.splice(
                        index,
                        1
                    );

                    renderAttachments(
                        attachmentsFileName,
                        attachmentsList
                    );
                }
            );

            item.appendChild(fileName);
            item.appendChild(removeButton);

            attachmentsList.appendChild(item);
        }
    );
}


export function getSelectedAttachments() {
    return [...selectedAttachments];
}
