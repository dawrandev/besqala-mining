<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy (`manage`) is enforced in the controller with the bound model.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['suspend', 'restore', 'terminate', 'annul', 'reissue'])],
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reason' => __('Asos'),
        ];
    }
}
