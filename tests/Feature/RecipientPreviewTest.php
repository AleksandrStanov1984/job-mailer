<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RecipientPreviewTest extends TestCase
{
    public function test_valid_recipient_json_is_parsed_correctly(): void
    {
        $json = json_encode([
            [
                'company' => 'Firma Alpha GmbH',
                'email' => 'alpha@example.de',
                'vacancy' => 'Produktionsmitarbeiter',
                'contact_name' => 'Anna Müller',
                'contact_salutation' => 'Frau',
            ],
            [
                'company' => 'Firma Beta GmbH',
                'email' => 'beta@example.de',
                'vacancy' => 'PHP Entwickler',
            ],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent(
                'recipients.json',
                $json
            );

        $response = $this->postJson(
            '/recipients/preview',
            [
                'recipients' => $file,
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'count' => 2,
            'duplicates' => 0,
            'valid_for_sending' => 2,
        ]);

        $response->assertJsonPath(
            'recipients.0.company',
            'Firma Alpha GmbH'
        );

        $response->assertJsonPath(
            'recipients.0.email',
            'alpha@example.de'
        );

        $response->assertJsonPath(
            'recipients.0.normalized_email',
            'alpha@example.de'
        );

        $response->assertJsonPath(
            'recipients.0.vacancy',
            'Produktionsmitarbeiter'
        );

        $response->assertJsonPath(
            'recipients.0.contact_name',
            'Anna Müller'
        );

        $response->assertJsonPath(
            'recipients.0.contact_salutation',
            'Frau'
        );

        $response->assertJsonPath(
            'recipients.0.status',
            'pending'
        );
    }


    public function test_duplicate_email_inside_json_is_marked_as_duplicate(): void
    {
        $json = json_encode([
            [
                'company' => 'Firma Eins',
                'email' => 'TEST@example.de',
                'vacancy' => 'Mitarbeiter',
            ],
            [
                'company' => 'Firma Zwei',
                'email' => ' test@example.de ',
                'vacancy' => 'Mitarbeiter',
            ],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent(
                'recipients.json',
                $json
            );

        $response = $this->postJson(
            '/recipients/preview',
            [
                'recipients' => $file,
            ]
        );

        $response->assertOk();

        $response->assertJson([
            'count' => 2,
            'duplicates' => 1,
            'valid_for_sending' => 1,
        ]);

        $response->assertJsonPath(
            'recipients.0.status',
            'pending'
        );

        $response->assertJsonPath(
            'recipients.1.status',
            'duplicate_in_file'
        );

        $response->assertJsonPath(
            'recipients.0.normalized_email',
            'test@example.de'
        );

        $response->assertJsonPath(
            'recipients.1.normalized_email',
            'test@example.de'
        );
    }


    public function test_optional_fields_may_be_missing(): void
    {
        $json = json_encode([
            [
                'email' => 'job@example.de',
            ],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent(
                'recipients.json',
                $json
            );

        $response = $this->postJson(
            '/recipients/preview',
            [
                'recipients' => $file,
            ]
        );

        $response->assertOk();

        $response->assertJsonPath(
            'recipients.0.email',
            'job@example.de'
        );

        $response->assertJsonPath(
            'recipients.0.company',
            null
        );

        $response->assertJsonPath(
            'recipients.0.vacancy',
            null
        );

        $response->assertJsonPath(
            'recipients.0.contact_name',
            null
        );

        $response->assertJsonPath(
            'recipients.0.contact_salutation',
            null
        );

        $response->assertJsonPath(
            'recipients.0.status',
            'pending'
        );
    }


    public function test_invalid_email_is_rejected(): void
    {
        $json = json_encode([
            [
                'company' => 'Firma Fehler',
                'email' => 'kein-email',
            ],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent(
                'recipients.json',
                $json
            );

        $response = $this->postJson(
            '/recipients/preview',
            [
                'recipients' => $file,
            ]
        );

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' =>
                'Строка 1: некорректный email kein-email.',
        ]);
    }


    public function test_missing_email_is_rejected(): void
    {
        $json = json_encode([
            [
                'company' => 'Firma Ohne Email',
            ],
        ]);

        $file = UploadedFile::fake()
            ->createWithContent(
                'recipients.json',
                $json
            );

        $response = $this->postJson(
            '/recipients/preview',
            [
                'recipients' => $file,
            ]
        );

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' =>
                'Строка 1: отсутствует email.',
        ]);
    }
}
