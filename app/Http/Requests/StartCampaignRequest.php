<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StartCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipients_json' => [
                'required',
                'string',
            ],

            'subject_template' => [
                'required',
                'string',
                'max:255',
            ],

            'message_template' => [
                'required',
                'string',
            ],

            'template_original_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'duplicate_protection_enabled' => [
                'required',
                'boolean',
            ],

            'duplicate_protection_days' => [
                'required',
                'integer',
                'min:1',
                'max:3650',
            ],

            'delay_seconds' => [
                'required',
                'integer',
                'min:0',
                'max:3600',
            ],

            'attachments' => [
                'nullable',
                'array',
                'max:10',
            ],

            'attachments.*' => [
                'file',
                File::types([
                    'pdf',
                    'doc',
                    'docx',
                    'jpg',
                    'jpeg',
                    'png',
                ])->max('20mb'),
            ],
        ];
    }
}
