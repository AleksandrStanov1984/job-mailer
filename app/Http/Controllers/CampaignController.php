<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Throwable;

class CampaignController extends Controller
{
    public function store(
        StartCampaignRequest $request,
        CampaignService $service,
    ): JsonResponse {
        try {
            $campaign = $service->create(
                $request->validated(),
                $request->file(
                    'attachments',
                    []
                )
            );

            return response()->json([
                'message' =>
                    'Кампания создана.',

                'campaign' => [
                    'id' => $campaign->id,

                    'delay_seconds' =>
                        $campaign->delay_seconds,

                    'started_at' =>
                        $campaign->started_at
                            ?->toIso8601String(),

                    'recipients' =>
                        $campaign->recipients
                            ->map(
                                fn (
                                    CampaignRecipient $recipient
                                ) => $this->recipientData(
                                    $recipient
                                )
                            )
                            ->values()
                            ->all(),
                ],
            ]);

        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' =>
                    $exception->getMessage(),
            ], 422);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Не удалось создать кампанию.',
            ], 500);
        }
    }

    public function sendRecipient(
        Campaign $campaign,
        CampaignRecipient $recipient,
        CampaignService $service,
    ): JsonResponse {
        $recipient =
            $service->sendRecipient(
                $campaign,
                $recipient
            );

        return response()->json([
            'recipient' =>
                $this->recipientData(
                    $recipient
                ),
        ]);
    }

    public function retryRecipient(
        Campaign $campaign,
        CampaignRecipient $recipient,
        CampaignService $service,
    ): JsonResponse {
        $recipient =
            $service->retryRecipient(
                $campaign,
                $recipient
            );

        return response()->json([
            'recipient' =>
                $this->recipientData(
                    $recipient
                ),
        ]);
    }

    private function recipientData(
        CampaignRecipient $recipient
    ): array {
        return [
            'id' => $recipient->id,

            'company' =>
                $recipient->company,

            'email' =>
                $recipient->email,

            'normalized_email' =>
                $recipient->normalized_email,

            'vacancy' =>
                $recipient->vacancy,

            'contact_name' =>
                $recipient->contact_name,

            'contact_salutation' =>
                $recipient->contact_salutation,

            'status' =>
                $recipient->status,

            'sent_at' =>
                $recipient->sent_at
                    ?->toIso8601String(),

            'failed_at' =>
                $recipient->failed_at
                    ?->toIso8601String(),

            'skipped_at' =>
                $recipient->skipped_at
                    ?->toIso8601String(),

            'error_message' =>
                $recipient->error_message,
        ];
    }
}
