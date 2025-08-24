<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

abstract class JsonRepository
{
    protected string $sourceName;
    protected string $filePath;
    protected string $cacheKey;

    public function __construct(string $sourceName)
    {
        $this->sourceName = $sourceName;
        $this->filePath = storage_path("app/public/json/{$this->sourceName}.json");
        $this->cacheKey = "{$this->sourceName}.json.data";
    }

    /**
     * Get all items, using cache to avoid repeated file reads.
     * @return array
     */
    public function all(): array
    {
        return Cache::rememberForever($this->cacheKey, function () {
            if (!File::exists($this->filePath)) {
                return [];
            }
            return json_decode(File::get($this->filePath), true) ?? [];
        });
    }

    public function findBy(string $key, string $value): ?array
    {
        return collect($this->all())->firstWhere($key, $value);
    }

    public function find(string $name): ?array
    {
        return collect($this->all())->firstWhere('name', $name);
    }

    public function create(array $data): void
    {
        $items = $this->all();
        $items[] = $data;
        $this->save($items);
    }

    public function update(string $key, string $value, array $data): bool
    {
        $items = $this->all();
        $index = collect($items)->search(fn($item) => isset($item[$key]) && $item[$key] === $value);

        if ($index === false) {
            return false;
        }
        
        $items[$index] = array_merge($items[$index], $data);
        $this->save($items);

        return true;
    }

    public function delete(string $key, string $value): bool
    {
        $items = $this->all();
        $originalCount = count($items);

        $newItems = collect($items)
            ->reject(fn ($item) => isset($item[$key]) && $item[$key] === $value)
            ->all();

        if (count($newItems) < $originalCount) {
            $this->save($newItems);
            return true;
        }

        return false;
    }

    protected function save(array $items): void
    {
        File::ensureDirectoryExists(dirname($this->filePath));
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        File::put($this->filePath, json_encode(array_values($items), $options));
        Cache::forget($this->cacheKey);
    }
}