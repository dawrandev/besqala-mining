<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Region;
use App\Enums\ResidentStatus;
use Database\Factories\ResidentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $company_name
 * @property string $stir
 * @property Region $region
 * @property string $legal_form
 * @property ResidentStatus $status
 * @property string $address
 * @property string|null $location_address
 * @property string|null $email
 * @property string|null $phone
 * @property array<int, array{name: string, share: int}>|null $founders
 * @property string|null $energy_source
 * @property string|null $cadastre_number
 * @property string|null $btc_address
 * @property \Illuminate\Support\Carbon|null $registered_at
 */
#[Fillable([
    'owner_id', 'company_name', 'stir', 'region', 'legal_form', 'status',
    'address', 'location_address', 'cadastre_number', 'email', 'phone', 'founders',
    'power_mw', 'energy_source', 'btc_address', 'mined_total_btc', 'registered_at',
])]
class Resident extends Model
{
    /** @use HasFactory<ResidentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'region' => Region::class,
            'status' => ResidentStatus::class,
            'founders' => 'array',
            'power_mw' => 'decimal:2',
            'mined_total_btc' => 'decimal:4',
            'registered_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return HasMany<Application, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /** @return HasMany<Document, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** @return HasMany<Inspection, $this> */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /** @return HasMany<Document, $this> */
    public function certificates(): HasMany
    {
        return $this->documents()->where('kind', 'certificate');
    }
}
