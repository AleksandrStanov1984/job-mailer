<?php

namespace Tests\Feature;

use Tests\TestCase;

class HistoryPageTest extends TestCase
{
    public function test_history_page_is_available(): void
    {
        $response = $this->get('/history');

        $response->assertOk();

        $response->assertSee(
            'История рассылок'
        );

        $response->assertSee(
            'На главную'
        );
    }

    public function test_history_page_contains_filters(): void
    {
        $response = $this->get('/history');

        $response->assertOk();

        $response->assertSee(
            'История рассылок'
        );

        /*
         * Здесь мы проверяем именно элементы,
         * которые уже должны присутствовать
         * на странице истории.
         */

        $response->assertSee(
            'Статус'
        );

        $response->assertSee(
            'Дата'
        );
    }
}
