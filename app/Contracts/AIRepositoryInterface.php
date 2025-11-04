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
    /**
     * @return array<string>
     */
    public function getAvailableProfiles(): array;

    /**
     * @return array<string>
     */
    public function suggestPrompts(array $context): array;

    /**
     * Get all available models from all configured providers.
     */
    public function getAvailableModels(): array;
}
