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
    | Supported: "openai", "gemini", "anthropic", "mistral"
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
        'mistral' => env('AI_MODEL_MISTRAL', 'mistral-large-latest'),
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
        'explanation' => <<<PROMPT
            Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, 
            voor een leek (de lezer aan wie je dit uitlegt), uit wat :input betekent - in zover 
            relevant, met betrekking to web development, grafische vormgeving of aanverwante software 
            voor teams (je hoeft dit verder niet te benoemen)
            PROMPT,
        'summarize' => <<<PROMPT
            Geef een korte samenvatting (in niet al te veel woorden, maximaal enkele regels) van de volgende tekst 
            alsof ik het aan iemand vertel over mijn tekst : :input
            PROMPT,
        'suggest' => <<<PROMPT
            You are an assistant that suggests relevant next prompts for a user in a chat conversation.
            Based on the provided chat history and context, suggest up to 3 short, relevant follow-up questions or prompts.
            The suggestions should be things the user might want to ask next.
            The suggestions should be in the same language as the last user message in the history.
            Return a JSON object with a single key "suggestions" which is an array of strings. For example: {"suggestions": ["What is a closure?", "Explain promises.", "How do I use flexbox?"]}
            If you have no suggestions, the "suggestions" array should be empty.
            If the chat history is empty, provide some general conversation starters related to web development, graphic design, or team software.
            Do not add any other text, just the JSON object.
            Chat History:
            ---
            :history
            ---
            PROMPT,
    ],
];
