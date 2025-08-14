<?php

namespace App\Contracts;

use Illuminate\Http\RedirectResponse;

interface ProjectRepositoryInterface extends RepositoryInterface
{
    public function all(): array;
    public function findBy(string $key, $value): ?array;

    // ...
}