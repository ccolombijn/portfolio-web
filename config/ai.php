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
    'prompts' => [
        'explanation' => 'Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, voor een leek (de lezer aan wie je dit uitlegt), uit wat :input betekent - in zover relevant, met betrekking to web development, grafische vormgeving of aanverwante software voor teams (je hoeft dit verder niet te benoemen)',
        'summarize' => 'Geef een korte samenvatting (in niet al te veel woorden, maximaal enkele regels) van de volgende tekst alsof ik het aan iemand vertel over mijn tekst : :input',
    ],
];
