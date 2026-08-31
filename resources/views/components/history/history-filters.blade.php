<div class="mailer-panel">

    <h2 class="mailer-section-title">
        Фильтры
    </h2>

    <form
        class="history-filters"
        method="GET"
        action="{{ route('mailer.history') }}"
    >

        <div class="history-filter-field">

            <label for="history_search">
                Поиск
            </label>

            <input
                class="mailer-input"
                type="text"
                id="history_search"
                name="search"
                value="{{ $historyFilters['search'] ?? '' }}"
                placeholder="Email, компания, вакансия..."
            >

        </div>


        <div class="history-filter-field">

            <label for="history_status">
                Статус
            </label>

            <select
                class="mailer-input"
                id="history_status"
                name="status"
            >
                <option
                    value="all"
                    @selected(
                        ($historyFilters['status'] ?? 'all') === 'all'
                    )
                >
                    Все
                </option>

                <option
                    value="sent"
                    @selected(
                        ($historyFilters['status'] ?? '') === 'sent'
                    )
                >
                    Отправлено
                </option>

                <option
                    value="failed"
                    @selected(
                        ($historyFilters['status'] ?? '') === 'failed'
                    )
                >
                    Ошибки
                </option>

                <option
                    value="skipped"
                    @selected(
                        ($historyFilters['status'] ?? '') === 'skipped'
                    )
                >
                    Пропущено
                </option>

                <option
                    value="pending"
                    @selected(
                        ($historyFilters['status'] ?? '') === 'pending'
                    )
                >
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
                name="date_from"
                value="{{ $historyFilters['date_from'] ?? '' }}"
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
                name="date_to"
                value="{{ $historyFilters['date_to'] ?? '' }}"
            >

        </div>


        <div class="history-filter-field">

            <label for="history_sort">
                Сортировка
            </label>

            <select
                class="mailer-input"
                id="history_sort"
                name="sort"
            >
                <option
                    value="desc"
                    @selected(
                        ($historyFilters['sort'] ?? 'desc') === 'desc'
                    )
                >
                    Сначала новые
                </option>

                <option
                    value="asc"
                    @selected(
                        ($historyFilters['sort'] ?? '') === 'asc'
                    )
                >
                    Сначала старые
                </option>
            </select>

        </div>


        <div class="history-filter-actions">

            <button
                type="submit"
                class="mailer-button mailer-button-primary"
                id="history-apply-filters"
            >
                Применить
            </button>

            <a
                href="{{ route('mailer.history') }}"
                class="mailer-button"
                id="history-reset-filters"
            >
                Сбросить
            </a>

        </div>

    </form>

</div>
