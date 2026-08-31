<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <title>{{ $title ?? 'Рассылка' }}</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body>

<div
    class="app-toast app-toast-success"
    id="app-toast"
    role="status"
>
    <span class="app-toast-icon">✓</span>
    <span id="app-toast-message"></span>
</div>

@if ($page === 'history')

    <x-history.history-page
        :history-recipients="$historyRecipients"
        :history-filters="$historyFilters"
    />

@else

    <x-mailer.main-page />

@endif

</body>

</html>
