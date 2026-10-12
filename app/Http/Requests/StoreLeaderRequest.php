<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaderRequest extends FormRequest
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
            'full_name' => ['required', 'array'],
            'full_name.uz' => ['required', 'string', 'max:255'],
            'full_name.ru' => ['nullable', 'string', 'max:255'],
            'full_name.en' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'array'],
            'position.uz' => ['required', 'string', 'max:255'],
            'position.ru' => ['nullable', 'string', 'max:255'],
            'position.en' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'array'],
            'department.*' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'array'],
            'bio.*' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'full_name.uz' => __('F.I.Sh. (uz)'),
            'position.uz' => __('Lavozim (uz)'),
        ];
    }
}
