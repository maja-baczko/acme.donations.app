<?php

namespace App\Modules\Administration\Services;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class SystemSettingService {
    protected string $cachePrefix = 'system_setting_';
    protected int $cacheTtl = 3600; // 1 hour

    /**
     * Create a new system setting
     *
     * @param array $data
     * @return SystemSetting
     * @throws Throwable
     */
    public function create(array $data): SystemSetting {
        return DB::transaction(function () use ($data) {
            $setting = SystemSetting::create($data);

            // Clear cache for this key
            if (isset($data['key'])) {
                Cache::forget($this->cachePrefix . $data['key']);
            }

            return $setting->fresh();
        });
    }

    /**
     * Update an existing system setting
     *
     * @param SystemSetting $setting
     * @param array $data
     * @return SystemSetting
     * @throws Throwable
     */
    public function update(SystemSetting $setting, array $data): SystemSetting {
        return DB::transaction(function () use ($setting, $data) {
            $oldKey = $setting->key;

            $setting->update($data);

            // Clear cache for old and new keys
            Cache::forget($this->cachePrefix . $oldKey);
            if (isset($data['key']) && $data['key'] !== $oldKey) {
                Cache::forget($this->cachePrefix . $data['key']);
            }

            return $setting->fresh();
        });
    }

    /**
     * Delete a system setting
     *
     * @param SystemSetting $setting
     * @return bool
     * @throws Throwable
     */
    public function delete(SystemSetting $setting): bool {
        return DB::transaction(function () use ($setting) {
            $key = $setting->key;

            $result = $setting->delete();

            // Clear cache
            Cache::forget($this->cachePrefix . $key);

            return $result;
        });
    }

    /**
     * Get setting value by key with type casting
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed {
        // Try to get from cache first
        return Cache::remember(
            $this->cachePrefix . $key,
            $this->cacheTtl,
            function () use ($key, $default) {
                $setting = SystemSetting::where('key', $key)->first();

                if (!$setting) {
                    return $default;
                }

                return $this->castValue($setting->value, $setting->type);
            }
        );
    }

    /**
     * Set setting value (create or update)
     *
     * @param string $key
     * @param mixed $value
     * @return SystemSetting
     * @throws Throwable
     */
    public function set(string $key, mixed $value): SystemSetting {
        return DB::transaction(function () use ($key, $value) {
            $setting = SystemSetting::where('key', $key)->first();

            // Determine type
            $type = $this->determineType($value);

            // Convert value to string for storage
            $valueString = $this->convertToString($value, $type);

            if ($setting) {
                // Update existing
                $setting->update([
                    'value' => $valueString,
                    'type' => $type,
                ]);
            } else {
                // Create new
                $setting = SystemSetting::create([
                    'key' => $key,
                    'value' => $valueString,
                    'type' => $type,
                    'is_public' => false,
                ]);
            }

            // Clear cache
            Cache::forget($this->cachePrefix . $key);

            return $setting->fresh();
        });
    }

    /**
     * Get all public settings
     *
     * @return Collection
     */
    public function getPublicSettings(): Collection {
        return SystemSetting::where('is_public', true)->get();
    }

    /**
     * Get all settings as key-value pairs
     *
     * @return Collection
     */
    public function getAllSettings(): Collection {
        return SystemSetting::all();
    }

    /**
     * Delete setting by key
     *
     * @param string $key
     * @return bool
     * @throws Throwable
     */
    public function forget(string $key): bool {
        return DB::transaction(function () use ($key) {
            $setting = SystemSetting::where('key', $key)->first();

            if (!$setting) {
                return false;
            }

            $result = $setting->delete();

            // Clear cache
            Cache::forget($this->cachePrefix . $key);

            return $result;
        });
    }

    /**
     * Check if setting exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool {
        return SystemSetting::where('key', $key)->exists();
    }

    /**
     * Cast value to proper type
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    protected function castValue(string $value, string $type): mixed {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Determine type from value
     *
     * @param mixed $value
     * @return string
     */
    protected function determineType(mixed $value): string {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string',
        };
    }

    /**
     * Convert value to string for storage
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    protected function convertToString(mixed $value, string $type): string {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'array' => json_encode($value),
            default => (string) $value,
        };
    }
}
