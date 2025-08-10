<?php

namespace App\Contracts;

interface RepositoryInterface
{
    public function all(): array;

    public function find(string $name): ?array;

    public function update(string $name, array $data): bool;

    public function create(array $data): void;

    public function delete(string $name): bool;
}