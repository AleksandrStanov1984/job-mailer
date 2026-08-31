<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewRecipientsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipients' => [
                'required',
                'file',
                'mimes:json',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipients.required' => 'Выберите JSON-файл с получателями.',
            'recipients.file' => 'Не удалось прочитать выбранный файл.',
            'recipients.mimes' => 'Файл получателей должен быть в формате JSON.',
            'recipients.max' => 'JSON-файл слишком большой. Максимальный размер — 10 MB.',
        ];
    }
}
