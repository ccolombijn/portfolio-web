<?php

namespace App\Contracts;

interface ProjectRepositoryInterface
{
    public function all(): array;
    public function findBy(string $key, $value): ?array;
    // ...
}