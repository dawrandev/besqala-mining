<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route is already guarded by the `role:applicant` middleware.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'stir' => ['required', 'string', 'size:9'],
            'region' => ['required', new Enum(Region::class)],
            'address' => ['required', 'string', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'cadastre_number' => ['nullable', 'string', 'max:60'],
            'property_right' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'power_mw' => ['nullable', 'numeric', 'min:0'],
            'energy_source' => ['nullable', 'string', 'max:60'],
            'bank_account' => ['nullable', 'string', 'max:60'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'btc_address' => ['nullable', 'string', 'max:80'],
            'founders' => ['nullable', 'array'],
            'founders.*.name' => ['nullable', 'string', 'max:255'],
            'founders.*.share' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'submit' => ['boolean'],
        ];
    }

    /**
     * Founders cleaned of empty rows, ready for the create action.
     *
     * @return list<array{name: string, share: int|string}>
     */
    public function founders(): array
    {
        $founders = $this->input('founders', []);

        return array_values(array_filter(
            is_array($founders) ? $founders : [],
            static fn ($f): bool => is_array($f) && ($f['name'] ?? '') !== '',
        ));
    }

    public function wantsSubmit(): bool
    {
        return $this->boolean('submit');
    }
}
