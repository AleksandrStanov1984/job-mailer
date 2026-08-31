<div class="mailer-container">

    <div class="history-page-header">

        <div class="history-heading">
            <h1 class="mailer-title">
                История рассылок
            </h1>

            <div class="mailer-muted">
                История отправок из локальной базы данных.
            </div>
        </div>

        <a
            href="{{ route('mailer.index') }}"
            class="mailer-button history-back-button"
        >
            ← На главную
        </a>

    </div>

    <x-history.history-filters />

    <x-history.history-table />

</div>
