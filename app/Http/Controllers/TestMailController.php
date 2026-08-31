<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\File;
use Throwable;

class TestMailController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
            ],

            'attachments' => [
                'nullable',
                'array',
                'max:10',
            ],

            'attachments.*' => [
                File::types([
                    'pdf',
                    'doc',
                    'docx',
                    'jpg',
                    'jpeg',
                    'png',
                ])->max('20mb'),
            ],
        ]);

        $temporaryFiles = [];

        try {
            foreach ($request->file('attachments', []) as $file) {
                $temporaryFiles[] = [
                    'path' => $file->getRealPath(),
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType()
                        ?: 'application/octet-stream',
                ];
            }

            Mail::to($validated['email'])
                ->send(
                    new TestMail(
                        mailSubject: $validated['subject'],
                        mailMessage: $validated['message'],
                        uploadedAttachments: $temporaryFiles,
                    )
                );

            return response()->json([
                'message' =>
                    'Тестовое письмо успешно отправлено.',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Ошибка отправки: ' .
                    $exception->getMessage(),
            ], 500);
        }
    }
}
