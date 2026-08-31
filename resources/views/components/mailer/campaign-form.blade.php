<div class="mailer-panel">

    <h2 class="mailer-section-title">
        Подготовка рассылки
    </h2>

    {{-- JSON + TXT --}}
    <div class="upload-grid">

        {{-- Получатели --}}
        <div class="upload-card">

            <div class="upload-card-title">
                Получатели
            </div>

            <div class="upload-card-description">
                JSON со списком предприятий
            </div>

            <div class="file-picker">
                <label
                    for="recipients"
                    class="file-picker-button"
                >
                    Выбрать JSON
                </label>

                <span
                    class="file-picker-name"
                    id="recipients-file-name"
                >
                    Файл не выбран
                </span>

                <input
                    class="file-picker-input"
                    type="file"
                    id="recipients"
                    name="recipients"
                    accept=".json,application/json"
                >
            </div>

            <div
                class="upload-result"
                id="recipients-info"
            >
                Компания · Email · Вакансия · Контактное лицо
            </div>

        </div>


        {{-- Текст письма --}}
        <div class="upload-card">

            <div class="upload-card-title">
                Текст письма
            </div>

            <div class="upload-card-description">
                Немецкий TXT-шаблон письма
            </div>

            <div class="file-picker">
                <label
                    for="template_file"
                    class="file-picker-button"
                >
                    Выбрать TXT
                </label>

                <span
                    class="file-picker-name"
                    id="template-file-name"
                >
                    Файл не выбран
                </span>

                <input
                    class="file-picker-input"
                    type="file"
                    id="template_file"
                    name="template_file"
                    accept=".txt,text/plain"
                >
            </div>

        </div>

    </div>


    {{-- Тема письма --}}
    <div class="mailer-field">

        <label for="subject_template">
            Тема письма
        </label>

        <input
            class="mailer-input"
            type="text"
            id="subject_template"
            name="subject_template"
            value="Bewerbung als @{{ vacancy }}"
        >

        <div class="mailer-muted">
            Название вакансии автоматически подставляется отдельно для каждого получателя из JSON.
        </div>

    </div>


    {{-- Текст письма --}}
    <div class="mailer-field">

        <label for="message">
            Письмо, которое будет отправлено
        </label>

        <textarea
            class="mailer-textarea"
            id="message"
            name="message"
            placeholder="Выберите TXT — его содержимое сразу появится здесь."
        ></textarea>

        <div class="mailer-muted">
            Текст можно изменить вручную перед отправкой.
        </div>

    </div>


    {{-- Вложения --}}
    <div class="mailer-field">

        <label>
            Вложения
        </label>

        <div class="file-picker">

            <label
                for="attachments"
                class="file-picker-button"
            >
                Добавить вложения
            </label>

            <span
                class="file-picker-name"
                id="attachments-file-name"
            >
                Файлы не выбраны
            </span>

            <input
                class="file-picker-input"
                type="file"
                id="attachments"
                name="attachments[]"
                multiple
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
            >

        </div>

        <div
            class="selected-files"
            id="attachments-list"
        ></div>

    </div>


    {{-- Тестовая отправка --}}
    <div class="mailer-field">

        <label for="test_email">
            Email для тестовой отправки
        </label>

        <input
            class="mailer-input"
            type="email"
            id="test_email"
            name="test_email"
            placeholder="Введите свой email"
        >

        <div class="mailer-muted">
            На этот адрес можно отправить одно тестовое письмо перед запуском рассылки.
        </div>

    </div>


    {{-- Действия --}}
    <div class="mailer-actions">

        <button
            class="mailer-button"
            type="button"
            id="send-test-button"
        >
            Отправить тестовое письмо
        </button>

        <button
            class="mailer-button mailer-button-primary"
            type="button"
            id="start-mailing-button"
        >
            Начать рассылку
        </button>

    </div>

</div>
