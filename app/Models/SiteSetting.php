<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $key
 * @property array<string, mixed> $value
 */
#[Fillable(['key', 'value'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Read a settings value by key.
     *
     * @return array<string, mixed>|null
     */
    public static function get(string $key): ?array
    {
        return static::query()->find($key)?->value;
    }

    /**
     * Create or update a settings value.
     *
     * @param  array<string, mixed>  $value
     */
    public static function put(string $key, array $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
