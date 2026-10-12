<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NewsCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreNewsRequest extends FormRequest
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
            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'array'],
            'body.uz' => ['required', 'string'],
            'body.ru' => ['nullable', 'string'],
            'body.en' => ['nullable', 'string'],
            'category' => ['required', new Enum(NewsCategory::class)],
            'published' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title.uz' => __('Sarlavha (uz)'),
            'body.uz' => __('Matn (uz)'),
        ];
    }
}
