<?php

namespace App\Services;

use Illuminate\Support\Str;
use InvalidArgumentException;

class RecipientJsonParser
{
    public function parse(string $contents): array
    {
        try {
            $rows = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            throw new InvalidArgumentException(
                'JSON содержит синтаксическую ошибку: ' . $exception->getMessage()
            );
        }

        if (! is_array($rows)) {
            throw new InvalidArgumentException(
                'JSON должен содержать массив получателей.'
            );
        }

        if ($rows === []) {
            throw new InvalidArgumentException(
                'JSON не содержит получателей.'
            );
        }

        $recipients = [];
        $errors = [];
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;

            if (! is_array($row)) {
                $errors[] = "Строка {$rowNumber}: получатель должен быть объектом.";

                continue;
            }

            $email = trim((string) ($row['email'] ?? ''));

            if ($email === '') {
                $errors[] = "Строка {$rowNumber}: отсутствует email.";

                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Строка {$rowNumber}: некорректный email {$email}.";

                continue;
            }

            $normalizedEmail = Str::lower($email);

            $isDuplicate = isset($seenEmails[$normalizedEmail]);

            if (! $isDuplicate) {
                $seenEmails[$normalizedEmail] = true;
            }

            $recipients[] = [
                'row' => $rowNumber,

                'company' => $this->nullableString(
                    $row['company'] ?? null
                ),

                'email' => $email,

                'normalized_email' => $normalizedEmail,

                'vacancy' => $this->nullableString(
                    $row['vacancy'] ?? null
                ),

                'contact_name' => $this->nullableString(
                    $row['contact_name'] ?? null
                ),

                'contact_salutation' => $this->nullableString(
                    $row['contact_salutation'] ?? null
                ),

                'status' => $isDuplicate
                    ? 'duplicate_in_file'
                    : 'pending',
            ];
        }

        if ($errors !== []) {
            throw new InvalidArgumentException(
                implode("\n", $errors)
            );
        }

        return $recipients;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : null;
    }
}
