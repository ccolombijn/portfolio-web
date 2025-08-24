<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class DatabasePageRepository implements RepositoryInterface
{
    /**
     * A unique key for caching the results from this repository.
     * @var string
     */
    private string $cacheKey = 'pages.db.data';

    /**
     * Get all pages, using a cache to avoid repeated database queries.
     */
    public function all(): array
    {
        return Cache::rememberForever($this->cacheKey, function () {
            // If the cache is empty, fetch all pages from the database and return them as an array.
            return Page::all()->toArray();
        });
    }

    /**
     * Find a specific page by its name from the cached collection.
     */
    public function find(string $name): ?array
    {
        // This is more efficient than a separate DB query as it uses the cached 'all()' result.
        return collect($this->all())->firstWhere('name', $name);
    }

    /**
     * Find a specific page by its name from the cached collection.
     */
    public function findBy(string $key, string $value): ?array
    {
        return collect($this->all())->firstWhere($key, $value);
    }

    /**
     * Create a new page in the database and clear the cache.
     */
    public function create(array $data): void
    {
        Page::create($data);

        Cache::forget($this->cacheKey);
    }

    /**
     * Update a page in the database and clear the cache.
     * @return bool
     */
    public function update(string $key, string $value, array $data): bool
    {
        $page = Page::where($key, $value)->first();

        if (!$page) {
            return false;
        }

        $updated = $page->update($data);

        if ($updated) {
            Cache::forget($this->cacheKey);
        }

        return $updated;
    }

    /**
     * Delete a page from the database and clear the cache.
     */
    public function delete(string $key, string $value): bool
    {
        $page = Page::where($key, 'like', $value)->first();

        if (!$page) {
            return false;
        }

        $deleted = $page->delete();

        if ($deleted) {
            Cache::forget($this->cacheKey);
        }

        return $deleted;
    }
}