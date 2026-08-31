<?php

namespace App\Services;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CampaignService
{
    public function __construct(
        private readonly CampaignTemplateRenderer $renderer,
    ) {
    }

    public function create(
        array $data,
        array $attachments = [],
    ): Campaign {
        $rawRecipients = json_decode(
            $data['recipients_json'],
            true
        );

        if (
            !is_array($rawRecipients) ||
            array_is_list($rawRecipients) === false
        ) {
            throw new \InvalidArgumentException(
                'Некорректный список получателей.'
            );
        }

        if ($rawRecipients === []) {
            throw new \InvalidArgumentException(
                'Список получателей пуст.'
            );
        }

        return DB::transaction(function () use (
            $data,
            $attachments,
            $rawRecipients
        ) {
            $campaign = Campaign::create([
                'name' => 'Рассылка ' .
                    now()->format('d.m.Y H:i:s'),

                'subject' =>
                    $data['subject_template'],

                'template_original_name' =>
                    $data['template_original_name']
                    ?? null,

                'duplicate_protection_enabled' =>
                    (bool) $data[
                        'duplicate_protection_enabled'
                    ],

                'duplicate_protection_days' =>
                    (int) $data[
                        'duplicate_protection_days'
                    ],

                'delay_seconds' =>
                    (int) $data['delay_seconds'],

                'started_at' => now(),
            ]);

            $this->createRecipients(
                $campaign,
                $rawRecipients,
                $data['subject_template'],
                $data['message_template'],
            );

            $this->storeAttachments(
                $campaign,
                $attachments
            );

            $this->finishIfDone($campaign);

            return $campaign->fresh([
                'recipients',
            ]);
        });
    }

    private function createRecipients(
        Campaign $campaign,
        array $rawRecipients,
        string $subjectTemplate,
        string $messageTemplate,
    ): void {
        $seen = [];

        foreach ($rawRecipients as $row) {
            if (!is_array($row)) {
                continue;
            }

            $email = trim(
                (string) (
                    $row['email'] ?? ''
                )
            );

            if (
                $email === '' ||
                filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                ) === false
            ) {
                continue;
            }

            $normalizedEmail =
                mb_strtolower($email);

            $recipientData = [
                'company' => $this->nullableString(
                    $row['company'] ?? null
                ),

                'email' => $email,

                'normalized_email' =>
                    $normalizedEmail,

                'vacancy' => $this->nullableString(
                    $row['vacancy'] ?? null
                ),

                'contact_name' =>
                    $this->nullableString(
                        $row['contact_name'] ?? null
                    ),

                'contact_salutation' =>
                    $this->nullableString(
                        $row[
                            'contact_salutation'
                        ] ?? null
                    ),
            ];

            /*
            |--------------------------------------------------------------------------
            | Дубликат внутри текущего JSON
            |--------------------------------------------------------------------------
            */

            if (isset($seen[$normalizedEmail])) {
                $status =
                    CampaignRecipient::
                    STATUS_DUPLICATE_IN_FILE;

                $skippedAt = now();
            } elseif (
                $this->wasRecentlySent(
                    $campaign,
                    $normalizedEmail
                )
            ) {
                $status =
                    CampaignRecipient::
                    STATUS_SKIPPED_RECENTLY_SENT;

                $skippedAt = now();
            } else {
                $status =
                    CampaignRecipient::
                    STATUS_PENDING;

                $skippedAt = null;
            }

            $seen[$normalizedEmail] = true;

            $subjectRendered =
                $this->renderer->render(
                    $subjectTemplate,
                    $recipientData
                );

            $messageRendered =
                $this->renderer->render(
                    $messageTemplate,
                    $recipientData
                );

            $campaign->recipients()->create([
                ...$recipientData,

                'subject_rendered' =>
                    $subjectRendered,

                'message_rendered' =>
                    $messageRendered,

                'status' => $status,

                'skipped_at' =>
                    $skippedAt,
            ]);
        }

        if (!$campaign->recipients()->exists()) {
            throw new \InvalidArgumentException(
                'В списке нет корректных получателей.'
            );
        }
    }

    private function wasRecentlySent(
        Campaign $campaign,
        string $normalizedEmail,
    ): bool {
        if (
            !$campaign->
            duplicate_protection_enabled
        ) {
            return false;
        }

        $from = now()->subDays(
            $campaign->
            duplicate_protection_days
        );

        return CampaignRecipient::query()
            ->where(
                'normalized_email',
                $normalizedEmail
            )
            ->where(
                'status',
                CampaignRecipient::STATUS_SENT
            )
            ->whereNotNull('sent_at')
            ->where(
                'sent_at',
                '>=',
                $from
            )
            ->where(
                'campaign_id',
                '!=',
                $campaign->id
            )
            ->exists();
    }

    private function storeAttachments(
        Campaign $campaign,
        array $attachments,
    ): void {
        foreach ($attachments as $index => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $originalName =
                basename(
                    $file->getClientOriginalName()
                );

            $filename =
                sprintf(
                    '%02d_%s',
                    $index + 1,
                    Str::random(8) . '_' .
                    $originalName
                );

            $file->storeAs(
                "campaigns/{$campaign->id}/attachments",
                $filename,
                'local'
            );
        }
    }

    public function sendRecipient(
        Campaign $campaign,
        CampaignRecipient $recipient,
    ): CampaignRecipient {
        if (
            $recipient->campaign_id !==
            $campaign->id
        ) {
            abort(404);
        }

        if (
            $recipient->status !==
            CampaignRecipient::STATUS_PENDING
        ) {
            return $recipient;
        }

        $recipient->update([
            'status' =>
                CampaignRecipient::STATUS_SENDING,

            'error_message' => null,
        ]);

        try {
            $attachments =
                Storage::disk('local')->files(
                    "campaigns/{$campaign->id}/attachments"
                );

            Mail::to(
                $recipient->email
            )->send(
                new CampaignMail(
                    mailSubject:
                        $recipient->subject_rendered,

                    mailMessage:
                        $recipient->message_rendered,

                    attachmentPaths:
                        $attachments,
                )
            );

            $recipient->update([
                'status' =>
                    CampaignRecipient::STATUS_SENT,

                'sent_at' => now(),

                'failed_at' => null,

                'error_message' => null,
            ]);

        } catch (Throwable $exception) {
            report($exception);

            $recipient->update([
                'status' =>
                    CampaignRecipient::STATUS_FAILED,

                'failed_at' => now(),

                'error_message' =>
                    Str::limit(
                        $exception->getMessage(),
                        2000
                    ),
            ]);
        }

        $this->finishIfDone($campaign);

        return $recipient->fresh();
    }

    public function retryRecipient(
        Campaign $campaign,
        CampaignRecipient $recipient,
    ): CampaignRecipient {
        if (
            $recipient->campaign_id !==
            $campaign->id
        ) {
            abort(404);
        }

        if (
            $recipient->status !==
            CampaignRecipient::STATUS_FAILED
        ) {
            return $recipient;
        }

        $recipient->update([
            'status' =>
                CampaignRecipient::STATUS_PENDING,

            'failed_at' => null,

            'error_message' => null,
        ]);

        if ($campaign->finished_at !== null) {
            $campaign->update([
                'finished_at' => null,
            ]);
        }

        return $this->sendRecipient(
            $campaign,
            $recipient->fresh()
        );
    }

    private function finishIfDone(
        Campaign $campaign
    ): void {
        $unfinished = $campaign->recipients()
            ->whereIn(
                'status',
                [
                    CampaignRecipient::STATUS_PENDING,
                    CampaignRecipient::STATUS_SENDING,
                ]
            )
            ->exists();

        if ($unfinished) {
            return;
        }

        $campaign->update([
            'finished_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Очистка временных файлов кампании
        |--------------------------------------------------------------------------
        |
        | После завершения рассылки физические вложения больше не нужны.
        | История, статусы, тема и текст письма остаются в SQLite.
        |
        | Даже если письмо завершилось со статусом failed, файлы удаляем:
        | при новой рассылке пользователь снова выбирает необходимые документы.
        |
        */

        Storage::disk('local')->deleteDirectory(
            "campaigns/{$campaign->id}"
        );
    }

    private function nullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }
}
