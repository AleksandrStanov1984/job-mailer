<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewRecipientsRequest;
use App\Services\RecipientJsonParser;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class RecipientImportController extends Controller
{
    public function preview(
        PreviewRecipientsRequest $request,
        RecipientJsonParser $parser
    ): JsonResponse {
        $file = $request->file('recipients');

        try {
            $recipients = $parser->parse(
                $file->get()
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $duplicates = collect($recipients)
            ->where('status', 'duplicate_in_file')
            ->count();

        return response()->json([
            'file_name' => $file->getClientOriginalName(),

            'count' => count($recipients),

            'duplicates' => $duplicates,

            'valid_for_sending' => count($recipients) - $duplicates,

            'recipients' => $recipients,
        ]);
    }
}
