<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy authorisation (`act`) is enforced in the controller so the
        // bound Application model is available there.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['submit', 'expert', 'deficiency', 'conclude', 'permit', 'reject'])],
            'comment' => ['nullable', 'string', 'max:2000'],
            'verdict' => ['required_if:action,conclude', Rule::in(['positive', 'negative'])],
            'deficiency_text' => ['required_if:action,deficiency', 'nullable', 'string', 'max:4000'],
        ];
    }

    /**
     * Deficiency items split into a clean list (one per line).
     *
     * @return list<string>
     */
    public function deficiencyItems(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->input('deficiency_text', '')) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn (string $i): bool => $i !== ''));
    }
}
