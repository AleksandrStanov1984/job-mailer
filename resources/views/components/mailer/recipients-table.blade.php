<div class="mailer-panel">

    <div class="recipients-header">

        <h2 class="mailer-section-title recipients-title">
            Получатели кампании
        </h2>

        <a
            href="{{ route('mailer.history') }}"
            class="mailer-button recipients-history-button"
        >
            История рассылок
        </a>

    </div>

    <x-mailer.stats />


    <div class="mailer-table-wrapper">

        <table class="mailer-table">

            <thead>
            <tr>
                <th style="width: 50px;">
                    №
                </th>

                <th>
                    Предприятие
                </th>

                <th>
                    Email
                </th>

                <th>
                    Вакансия
                </th>

                <th style="width: 190px;">
                    Статус
                </th>

                <th style="width: 260px;">
                    Дата / ошибка
                </th>
            </tr>
            </thead>


            <tbody id="recipients-table-body">

            <tr
                id="recipients-empty-row"
                data-empty-row="true"
            >
                <td colspan="6">
                    Получатели пока не загружены.
                </td>
            </tr>

            </tbody>

        </table>

    </div>

</div>
