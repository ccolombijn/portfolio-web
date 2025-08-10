<?php
namespace App\Contracts;

interface PageRepositoryInterface
{
    public function all(): array;
    public function findBy(string $key, $value): ?array;
    // ... add create, update, delete if you want them in the contract
}