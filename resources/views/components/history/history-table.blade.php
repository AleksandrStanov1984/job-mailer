<div class="mailer-panel">

    <div class="history-table-header">

        <h2 class="mailer-section-title">
            Отправки
        </h2>

        <span
            class="mailer-muted"
            id="history-results-count"
        >
            Записей:
            {{ $historyRecipients->count() }}
        </span>

    </div>


    <div class="mailer-table-wrapper">

        <table class="mailer-table">

            <thead>
            <tr>
                <th>№</th>
                <th>Дата</th>
                <th>Предприятие</th>
                <th>Email</th>
                <th>Вакансия</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>
            </thead>

            <tbody id="history-table-body">

            @forelse ($historyRecipients as $index => $recipient)

                <tr
                    data-status="{{ $recipient->status }}"
                >
                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        @if ($recipient->sent_at)
                            {{ $recipient->sent_at->format('d.m.Y H:i:s') }}

                        @elseif ($recipient->failed_at)
                            {{ $recipient->failed_at->format('d.m.Y H:i:s') }}

                        @elseif ($recipient->skipped_at)
                            {{ $recipient->skipped_at->format('d.m.Y H:i:s') }}

                        @else
                            {{ $recipient->created_at->format('d.m.Y H:i:s') }}
                        @endif
                    </td>

                    <td>
                        {{ $recipient->company ?: '—' }}
                    </td>

                    <td>
                        {{ $recipient->email }}
                    </td>

                    <td>
                        {{ $recipient->vacancy ?: '—' }}
                    </td>

                    <td>
                        @switch($recipient->status)

                            @case('sent')
                                <span class="status status-sent">
                                    Отправлено
                                </span>
                                @break

                            @case('failed')
                                <span class="status status-failed">
                                    Ошибка
                                </span>
                                @break

                            @case('pending')
                                <span class="status status-pending">
                                    Ожидает
                                </span>
                                @break

                            @case('sending')
                                <span class="status status-sending">
                                    Отправляется
                                </span>
                                @break

                            @case('skipped_recently_sent')
                                <span class="status status-skipped">
                                    Недавно отправляли
                                </span>
                                @break

                            @case('duplicate_in_file')
                                <span class="status status-duplicate">
                                    Дубликат в файле
                                </span>
                                @break

                            @default
                                {{ $recipient->status }}

                        @endswitch
                    </td>

                    <td>
                        @if ($recipient->status === 'failed')
                            <span
                                title="{{ $recipient->error_message }}"
                            >
                                Ошибка
                            </span>

                        @elseif ($recipient->subject_rendered)
                            <span
                                title="{{ $recipient->subject_rendered }}"
                            >
                                {{ $recipient->subject_rendered }}
                            </span>

                        @else
                            —
                        @endif
                    </td>
                </tr>

            @empty

                <tr id="history-empty-row">
                    <td colspan="7">
                        История пока пуста.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>
