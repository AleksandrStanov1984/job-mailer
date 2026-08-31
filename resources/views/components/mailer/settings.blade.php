<div class="mailer-panel">

    <h2 class="mailer-section-title">
        Настройки отправки
    </h2>

    <div class="settings-row">

        <label class="settings-checkbox">
            <input
                type="checkbox"
                id="duplicate_protection"
                checked
            >

            <span>Не отправлять повторно</span>
        </label>

        <div class="settings-small-field">
            <label for="duplicate_days">
                Период, дней
            </label>

            <input
                class="mailer-input"
                type="number"
                id="duplicate_days"
                value="7"
                min="1"
            >
        </div>

        <div class="settings-small-field">
            <label for="delay_seconds">
                Пауза, сек.
            </label>

            <input
                class="mailer-input"
                type="number"
                id="delay_seconds"
                value="5"
                min="0"
            >
        </div>

        <button
            type="button"
            class="mailer-button mailer-button-primary settings-save-button"
            id="save-settings-button"
        >
            Сохранить
        </button>

    </div>

</div>
