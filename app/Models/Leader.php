<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasTranslations;
use Database\Factories\LeaderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['full_name', 'position', 'department', 'bio', 'photo', 'sort_order'])]
class Leader extends Model
{
    /** @use HasFactory<LeaderFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'full_name' => 'array',
            'position' => 'array',
            'department' => 'array',
            'bio' => 'array',
        ];
    }
}
