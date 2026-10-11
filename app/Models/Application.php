<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\Region;
use App\Models\Builders\ApplicationBuilder;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $number
 * @property int $applicant_id
 * @property int|null $resident_id
 * @property ApplicationType $type
 * @property ApplicationStatus $status
 * @property string $company_name
 * @property string|null $stir
 * @property Region $region
 * @property string $legal_form
 * @property array<int, array{name: string, share: int}>|null $founders
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $decided_at
 *
 * @method static ApplicationBuilder<Application> query()
 */
#[Fillable([
    'number', 'applicant_id', 'resident_id', 'type', 'status',
    'company_name', 'stir', 'region', 'legal_form', 'address', 'location_address',
    'email', 'phone', 'founders', 'equipment', 'power_mw', 'energy_source',
    'bank_account', 'attachments', 'submitted_at', 'decided_at',
])]
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ApplicationType::class,
            'status' => ApplicationStatus::class,
            'region' => Region::class,
            'founders' => 'array',
            'equipment' => 'array',
            'attachments' => 'array',
            'power_mw' => 'decimal:2',
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return ApplicationBuilder<Application>
     */
    public function newEloquentBuilder($query): ApplicationBuilder
    {
        return new ApplicationBuilder($query);
    }

    /** @return BelongsTo<User, $this> */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /** @return BelongsTo<Resident, $this> */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /** @return HasMany<ApplicationEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(ApplicationEvent::class)->latest();
    }

    /** @return HasMany<ExpertConclusion, $this> */
    public function conclusions(): HasMany
    {
        return $this->hasMany(ExpertConclusion::class);
    }

    /** @return HasMany<Deficiency, $this> */
    public function deficiencies(): HasMany
    {
        return $this->hasMany(Deficiency::class);
    }

    /** @return HasMany<Document, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
