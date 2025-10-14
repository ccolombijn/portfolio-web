<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used by the
    | application. You can switch this to 'gemini' or any other provider
    | you add to the AIRepository.
    |
    | Supported: "openai", "gemini"
    |
    */

    'default_provider' => env('AI_PROVIDER', 'openai'),
];
