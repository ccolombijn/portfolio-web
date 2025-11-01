<?php

namespace App\Providers\Api;

use Anthropic;
use Anthropic\Client;
use Anthropic\Contracts\ClientContract;

class AnthropicServiceProvider extends ApiServiceProvider
{
    protected function getApiKeyConfigKey(): string
    {
        return 'anthropic.api_key';
    }

    protected function getRequestTimeoutConfigKey(): string
    {
        return 'anthropic.request_timeout';
    }

    protected function getClientContract(): string
    {
        return ClientContract::class;
    }

    protected function getClientClass(): string
    {
        return Client::class;
    }

    protected function getClientAlias(): string
    {
        return 'anthropic';
    }

    protected function getApiName(): string
    {
        return 'Anthropic';
    }

    protected function createClient(string $apiKey, int $requestTimeout): object
    {
        return Anthropic::factory()
            ->withApiKey($apiKey)
            ->withHttpHeader('anthropic-version', '2023-06-01')
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => $requestTimeout]))
            ->make();
    }
}