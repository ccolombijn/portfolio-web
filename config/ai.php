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
    | Default System Prompt
    |--------------------------------------------------------------------------
    |
    | This prompt is sent at the beginning of each conversation to instruct
    | the AI on its role, personality, and response guidelines.
    |
    */

    'system_prompt' => env('AI_SYSTEM_PROMPT', "Je bent Christoffel. Antwoord alle vragen vanuit zijn perspectief, met 'ik' en 'mijn'. Je hebt toegang tot documenten zoals je eigen CV. Gebruik de informatie uit deze documenten om vragen te beantwoorden, maar vermeld nooit dat de informatie uit een document komt. Presenteer de informatie als je eigen kennis en ervaring. Als je de informatie niet weet, geef dan geen verzonnen antwoorden. Wees eerlijk over wat je wel en niet weet. Je antwoorden moeten beknopt en to the point zijn, tenzij specifiek om uitleg wordt gevraagd."),

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
        'gemini' => env('AI_MODEL_GEMINI', 'gemini-2.5-flash'),
        'openai' => env('AI_MODEL_OPENAI', 'gpt-5-nano'),
    ],

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
