<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailerPageTest extends TestCase
{
    public function test_mailer_page_is_available(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_mailer_page_contains_required_controls(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $response->assertSee('Рассылка работодателям');

        $response->assertSee('id="recipients"', false);
        $response->assertSee('id="template_file"', false);
        $response->assertSee('id="subject_template"', false);
        $response->assertSee('id="message"', false);
        $response->assertSee('id="attachments"', false);

        $response->assertSee('id="test_email"', false);
        $response->assertSee('id="send-test-button"', false);
        $response->assertSee('id="start-mailing-button"', false);
    }

    public function test_mailer_page_contains_preview_block(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $response->assertSee(
            'Предпросмотр первого письма'
        );

        $response->assertSee(
            'id="preview-empty"',
            false
        );

        $response->assertSee(
            'id="preview-content"',
            false
        );

        $response->assertSee(
            'id="preview-company"',
            false
        );

        $response->assertSee(
            'id="preview-email"',
            false
        );

        $response->assertSee(
            'id="preview-vacancy"',
            false
        );

        $response->assertSee(
            'id="preview-contact"',
            false
        );

        $response->assertSee(
            'id="preview-subject"',
            false
        );

        $response->assertSee(
            'id="preview-message"',
            false
        );
    }

    public function test_mailer_page_contains_recipient_status_filters(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $response->assertSee(
            'data-status-filter="all"',
            false
        );

        $response->assertSee(
            'data-status-filter="sent"',
            false
        );

        $response->assertSee(
            'data-status-filter="failed"',
            false
        );

        $response->assertSee(
            'data-status-filter="skipped"',
            false
        );

        $response->assertSee(
            'data-status-filter="pending"',
            false
        );
    }
}
