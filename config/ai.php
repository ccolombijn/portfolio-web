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

    'default_provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Default AI Models
    |--------------------------------------------------------------------------
    |
    | This option controls the default models that will be used for each
    | provider. You can override these using the .env file.
    |
    */

    'models' => [
        'gemini' => env('AI_MODEL_GEMINI', 'gemini-2.5-flash-lite'),
        'openai' => env('AI_MODEL_OPENAI', 'gpt-5-nano'),
        'anthropic' => env('AI__MODEL_ANTHROPIC', 'claude-3-haiku-20240307'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default System Prompt
    |--------------------------------------------------------------------------
    |
    | This prompt is sent at the beginning of each conversation to instruct
    | the AI on its role, personality, and response guidelines.
    |
    */

    'system_prompt' => env('AI_SYSTEM_PROMPT', 'You are a helpful assistant specialized in web development, graphic design, and related software for teams. Provide clear, concise, and accurate information to assist users with their queries in these domains.'),

    /*
    |--------------------------------------------------------------------------
    | Default Files for AI Context
    |--------------------------------------------------------------------------
    |
    | Here you may specify default files from `storage/app/public` that should
    | always be included in the context of AI generation requests. These paths
    | are relative to `storage/app/public`.
    |
    */
    'default_files' => array_filter(explode(',', env('AI_DEFAULT_FILES', ''))),

    /*
    |--------------------------------------------------------------------------
    | Default AI Profile
    |--------------------------------------------------------------------------
    | Here you may specify the default AI profile to be used
    | throughout the application. This profile can define specific
    | settings or behaviors for AI interactions.
    |
    */

    'default_profile' => env('AI_DEFAULT_PROFILE', 'christoffel'),

    /*
    |--------------------------------------------------------------------------
    | Predefined Prompts
    |--------------------------------------------------------------------------
    | Here you may define some predefined prompts that can be used
    | throughout the application to standardize common AI requests.
    |
    */
    'prompts' => [
        'explanation' => 'Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, voor een leek (de lezer aan wie je dit uitlegt), uit wat :input betekent - in zover relevant, met betrekking to web development, grafische vormgeving of aanverwante software voor teams (je hoeft dit verder niet te benoemen)',
        'summarize' => 'Geef een korte samenvatting (in niet al te veel woorden, maximaal enkele regels) van de volgende tekst alsof ik het aan iemand vertel over mijn tekst : :input',
    ],
];
