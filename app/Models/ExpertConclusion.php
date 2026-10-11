<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExpertVerdict;
use Database\Factories\ExpertConclusionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['application_id', 'expert_id', 'verdict', 'comment', 'members', 'concluded_at'])]
class ExpertConclusion extends Model
{
    /** @use HasFactory<ExpertConclusionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verdict' => ExpertVerdict::class,
            'members' => 'array',
            'concluded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Application, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /** @return BelongsTo<User, $this> */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }
}
