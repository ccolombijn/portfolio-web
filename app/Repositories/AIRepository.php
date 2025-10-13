<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\AIRepositoryInterface;
use Gemini\Client as GeminiClient;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use OpenAI\Client as OpenAIClient;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class AIRepository implements AIRepositoryInterface
{
    public function __construct(
        private readonly OpenAIClient $openai,
        private readonly GeminiClient $gemini
    ) {}

    public function generate(array $data, ?string $provider = null): JsonResponse|StreamedResponse
    {
        $provider ??= config('ai.default_provider', 'openai');

        return match ($provider) {
            'openai' => $this->generateWithOpenAI($data),
            'gemini' => $this->generateWithGemini($data),
            default => throw new InvalidArgumentException("Unsupported AI provider: [{$provider}]")
        };
    }

    /**
     * Handles generation using the OpenAI client.
     */
    private function generateWithOpenAI(array $data): JsonResponse|StreamedResponse
    {
        $model = $data['model'] ?? config('openai.default_model', 'gpt-4o-mini');
        $messages = $this->buildOpenAIMessages($data);

        if (! empty($data['stream'])) {
            return $this->streamOpenAIResponse($model, $messages);
        }

        try {
            $result = $this->openai->chat()->create([
                'model' => $model,
                'messages' => $messages,
            ]);

            return response()->json(['response' => $result->choices[0]->message->content]);
        } catch (Throwable $e) {
            // Log the error for debugging
            return response()->json(['error' => 'Failed to get a response from OpenAI.'], 500);
        }
    }

    private function buildOpenAIMessages(array $data): array
    {
        $messages = [];

        if (! empty($data['history'])) {
            foreach ($data['history'] as $item) {
                $role = $item['role'] ?? 'user';
                if (in_array($role, ['user', 'assistant', 'system'])) {
                    $messages[] = ['role' => $role, 'content' => $item['text']];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => $data['prompt']];

        return $messages;
    }

    private function streamOpenAIResponse(string $model, array $messages): StreamedResponse
    {
        $stream = $this->openai->chat()->createStreamed([
            'model' => $model,
            'messages' => $messages,
        ]);

        return new StreamedResponse(function () use ($stream) {
            try {
                foreach ($stream as $result) {
                    $content = $result->choices[0]->delta->content;
                    if (null !== $content) {
                        echo $content;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
            } catch (Throwable $e) {
                // Log the error for debugging
                echo "[ERROR: An unexpected error occurred during the stream.]";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/plain',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Handles generation using the Gemini client.
     */
    private function generateWithGemini(array $data): JsonResponse|StreamedResponse
    {
        $model = $data['model'] ?? config('gemini.default_model', 'gemini-1.5-flash-latest');
        $prompt = $this->buildGeminiPrompt($data);

        if (! empty($data['stream'])) {
            return $this->streamGeminiResponse($model, $prompt);
        }

        // Non-streaming for Gemini (if ever needed)
        try {
            $result = $this->gemini->generativeModel($model)->generateContent($prompt);

            return response()->json(['response' => $result->text()]);
        } catch (Throwable $e) {
            // Log the error for debugging
            return response()->json(['error' => 'Failed to get a response from Gemini.'], 500);
        }
    }

    private function streamGeminiResponse(string $model, $prompt): StreamedResponse
    {
        $stream = $this->gemini->generativeModel($model)->streamGenerateContent($prompt);

        return new StreamedResponse(function () use ($stream) {
            try {
                foreach ($stream as $response) {
                    if (! empty($text = $response->text())) {
                        echo $text;
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
            } catch (Throwable $e) {
                // Handle specific Gemini errors or general stream errors
                echo "[ERROR: An unexpected error occurred during the stream.]";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/plain',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Build prompt with given data for Gemini.
     */
    private function buildGeminiPrompt(array $data)
    {
        if (isset($data['history'])) {
            $history = [];
            foreach ($data['history'] as $item) {
                $role = ($item['role'] ?? 'user') === 'model' ? Role::MODEL : Role::USER;
                $history[] = Content::parse(part: $item['text'], role: $role);
            }

            return [
                ...$history,
                Content::parse(part: $data['prompt'], role: Role::USER),
            ];
        }

        // This handles the specific prompts from your frontend like 'explanation' and 'summarize'
        if (isset($data['prompt_type'])) {
            $prompts = [
                'explanation' => 'Leg kort (in niet al te veel woorden), en in zo eenvoudig mogelijke bewoordingen, voor een leek (de lezer aan wie je dit uitlegt), uit wat ' . $data['input'] . ' betekent - in zover relevant, met betrekking to web development, grafische vormgeving of aanverwante software voor teams (je hoeft dit verder niet te benoemen)',
                'summarize' => 'Geef een korte samenvatting (in niet al te veel woorden, maximaal enkele regels) van de volgende tekst alsof ik het aan iemand vertel over mijn tekst : ' . $data['input'],
            ];

            if (isset($prompts[$data['prompt_type']])) {
                return $prompts[$data['prompt_type']];
            }
        }

        // Fallback to the main prompt if no specific type is matched
        return $data['prompt'];
    }
}
