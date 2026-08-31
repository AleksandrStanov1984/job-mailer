<?php

namespace App\Services;

class CampaignTemplateRenderer
{
    public function render(
        string $template,
        array $recipient
    ): string {
        $variables = [
            'company' => $recipient['company'] ?? '',
            'email' => $recipient['email'] ?? '',
            'vacancy' => $recipient['vacancy'] ?? '',
            'contact_name' => $recipient['contact_name'] ?? '',
            'contact_salutation' => $recipient['contact_salutation'] ?? '',
            'greeting' => $this->buildGreeting($recipient),
        ];

        return preg_replace_callback(
            '/{{\s*([a-zA-Z0-9_]+)\s*}}/',
            static function (array $matches) use ($variables): string {
                return (string) (
                    $variables[$matches[1]] ?? ''
                );
            },
            $template
        );
    }

    private function buildGreeting(
        array $recipient
    ): string {
        $name = trim(
            (string) (
                $recipient['contact_name'] ?? ''
            )
        );

        $salutation = trim(
            (string) (
                $recipient['contact_salutation'] ?? ''
            )
        );

        if ($name === '') {
            return 'Sehr geehrte Damen und Herren,';
        }

        $lastName = $this->lastName($name);

        if (strcasecmp($salutation, 'Frau') === 0) {
            return "Sehr geehrte Frau {$lastName},";
        }

        if (strcasecmp($salutation, 'Herr') === 0) {
            return "Sehr geehrter Herr {$lastName},";
        }

        return "Guten Tag {$name},";
    }

    private function lastName(
        string $name
    ): string {
        $parts = preg_split(
            '/\s+/u',
            trim($name)
        );

        return (string) end($parts);
    }
}
