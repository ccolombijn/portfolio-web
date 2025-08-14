<?php

namespace App\Contracts;

interface RepositoryInterface
{
    public function all(): array;

    public function find(string $name): ?array;

    public function findBy(string $key, string $value): ?array;

    public function update(string $name, string $value, array $data): bool;

    public function create(array $data): void;

    public function delete(string $key, string $name): bool;
}