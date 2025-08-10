<?php

namespace App\Contracts;

interface ContactRepositoryInterface
{
    public function getDetails(): array;
}