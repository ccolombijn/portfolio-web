<?php

namespace App\Providers\Api;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

abstract class ApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The configuration key for the API key.
     * e.g., 'anthropic.api_key'
     */
    abstract protected function getApiKeyConfigKey(): string;

    /**
     * The configuration key for the request timeout.
     * e.g., 'anthropic.request_timeout'
     */
    abstract protected function getRequestTimeoutConfigKey(): string;

    /**
     * The contract class for the client.
     * e.g., Anthropic\Contracts\ClientContract::class
     */
    abstract protected function getClientContract(): string;

    /**
     * The concrete class for the client.
     * e.g., Anthropic\Client::class
     */
    abstract protected function getClientClass(): string;

    /**
     * The alias for the client in the container.
     * e.g., 'anthropic'
     */
    abstract protected function getClientAlias(): string;

    /**
     * The name of the API for error messages.
     * e.g., 'Anthropic'
     */
    abstract protected function getApiName(): string;

    /**
     * Factory method to create the client instance.
     */
    abstract protected function createClient(string $apiKey, int $requestTimeout): object;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton($this->getClientContract(), function () {
            $apiKey = config($this->getApiKeyConfigKey());
            $timeout = (int) config($this->getRequestTimeoutConfigKey(), 30);

            if (! is_string($apiKey) || trim($apiKey) === '') {
                throw new InvalidArgumentException(
                    "The {$this->getApiName()} API Key is missing. Please set it in your .env file or `config/{$this->getClientAlias()}.php`."
                );
            }

            return $this->createClient($apiKey, $timeout);
        });

        $this->app->alias($this->getClientContract(), $this->getClientAlias());
        $this->app->alias($this->getClientContract(), $this->getClientClass());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            $this->getClientClass(),
            $this->getClientContract(),
            $this->getClientAlias(),
        ];
    }
}