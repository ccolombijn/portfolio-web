<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface AIRepositoryInterface
{
    /**
     * @return JsonResponse|StreamedResponse
     */
    public function generate(array $data, ?string $provider = null);
}
