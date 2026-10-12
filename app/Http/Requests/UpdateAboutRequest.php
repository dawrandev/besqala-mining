<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Guarded by the `role:admin` middleware on the route.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.uz' => ['required', 'string', 'max:255'],
            'title.ru' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'intro' => ['required', 'array'],
            'intro.uz' => ['required', 'string'],
            'intro.ru' => ['nullable', 'string'],
            'intro.en' => ['nullable', 'string'],
            'stats' => ['nullable', 'array'],
            'stats.*.label' => ['nullable', 'array'],
            'stats.*.label.*' => ['nullable', 'string', 'max:120'],
            'stats.*.value' => ['nullable', 'string', 'max:60'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title.uz' => __('Sarlavha (uz)'),
            'intro.uz' => __('Kirish matni (uz)'),
        ];
    }

    /**
     * Stats cleaned of empty rows.
     *
     * @return list<array{label: array<string, string>, value: string}>
     */
    public function stats(): array
    {
        $stats = $this->input('stats', []);

        return array_values(array_filter(
            is_array($stats) ? $stats : [],
            static fn ($s): bool => is_array($s) && ($s['value'] ?? '') !== '',
        ));
    }
}
