<?php

namespace App\Models;

use App\Support\FinanceDefaults;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Tutti i parametri, con i default per quelli mai salvati.
     *
     * @return array<string, float>
     */
    public static function values(): array
    {
        $stored = static::query()->pluck('value', 'key')->all();

        $values = [];
        foreach (FinanceDefaults::SETTINGS as $key => $default) {
            $values[$key] = (float) ($stored[$key] ?? $default);
        }

        return $values;
    }

    /**
     * @param  array<string, float|int|string>  $values
     */
    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            if (array_key_exists($key, FinanceDefaults::SETTINGS)) {
                static::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
            }
        }
    }
}
