<div class="mailer-panel">

    <h2 class="mailer-section-title">
        Фильтры
    </h2>

    <div class="history-filters">

        <div class="history-filter-field">

            <label for="history_status">
                Статус
            </label>

            <select
                class="mailer-input"
                id="history_status"
            >
                <option value="all">
                    Все
                </option>

                <option value="sent">
                    Отправлено
                </option>

                <option value="failed">
                    Ошибки
                </option>

                <option value="skipped">
                    Пропущено
                </option>

                <option value="pending">
                    Ожидает
                </option>
            </select>

        </div>


        <div class="history-filter-field">

            <label for="history_date_from">
                С даты
            </label>

            <input
                class="mailer-input"
                type="date"
                id="history_date_from"
            >

        </div>


        <div class="history-filter-field">

            <label for="history_date_to">
                По дату
            </label>

            <input
                class="mailer-input"
                type="date"
                id="history_date_to"
            >

        </div>


        <div class="history-filter-field">

            <label for="history_sort">
                Сортировка
            </label>

            <select
                class="mailer-input"
                id="history_sort"
            >
                <option value="desc">
                    Сначала новые
                </option>

                <option value="asc">
                    Сначала старые
                </option>
            </select>

        </div>


        <div class="history-filter-actions">

            <button
                type="button"
                class="mailer-button mailer-button-primary"
                id="history-apply-filters"
            >
                Применить
            </button>

            <button
                type="button"
                class="mailer-button"
                id="history-reset-filters"
            >
                Сбросить
            </button>

        </div>

    </div>

</div>
