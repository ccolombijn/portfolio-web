<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AIRepositoryInterface;
use Gemini\Client as GeminiClient;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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
        $provider ??= config('ai.default_provider', 'gemini');
        Log::debug('AI generation requested.', ['provider' => $provider, 'data' => $data]);
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
        try {
            $model = $data['model'] ?? config('openai.default_model', 'gpt-4o-mini');
            $messages = $this->buildOpenAIMessages($data);

            if (! empty($data['stream'])) {
                return $this->streamOpenAIResponse($model, $messages);
            }

            $result = $this->openai->chat()->create([
                'model' => $model,
                'messages' => $messages,
            ]);

            return response()->json(['response' => $result->choices[0]->message->content]);
        } catch (Throwable $e) {
            Log::error('Failed to get a response from OpenAI.', ['exception' => $e]);
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
                    $messages[] = ['role' => $role, 'content' => (string) ($item['text'] ?? '')];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => (string) ($data['prompt'] ?? '')];

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
                Log::error('An unexpected error occurred during the OpenAI stream.', ['exception' => $e]);
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
        // Non-streaming for Gemini (if ever needed)
        try {
            $model = $data['model'] ?? config('gemini.default_model', 'gemini-2.5-flash-lite');
            $prompt = $this->buildGeminiPrompt($data);

            if (! empty($data['stream'])) {
                return $this->streamGeminiResponse($model, $prompt);
            }

            $result = $this->gemini->generativeModel($model)->generateContent($prompt);
            return response()->json(['response' => $result->text()]);
        } catch (Throwable $e) {
            Log::error('Failed to get a response from Gemini.', ['exception' => $e]);
            return response()->json(['error' => 'Failed to get a response from Gemini.'], 500);
        }
    }

    private function streamGeminiResponse(string $model, $prompt): StreamedResponse
    {
        Log::debug('Gemini: Starting streaming response.', ['model' => $model, 'prompt' => $prompt]);
        // When $prompt is an array of Content objects, it must be spread.
        $stream = is_array($prompt)
            ? $this->gemini->generativeModel($model)->streamGenerateContent(...$prompt)
            : $this->gemini->generativeModel($model)->streamGenerateContent($prompt);

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
                Log::error('An unexpected error occurred during the Gemini stream.', ['exception' => $e]);
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
    private function buildGeminiPrompt(array $data): string|array
    {
        if (isset($data['history'])) {
            $history = array_map(function ($item) {
                $role = ($item['role'] ?? 'user') === 'model' ? Role::MODEL : Role::USER;
                $partText = (string) ($item['text'] ?? ''); // Ensure it's a string
                // Explicitly ensure it's a string, even if redundant, for defensive programming
                if (!is_string($partText)) {
                    Log::warning('Gemini: History item text is not a string after cast, forcing conversion.', ['original_type' => gettype($partText), 'original_value' => $partText]);
                    $partText = (string) $partText;
                }
                Log::debug('Gemini: Passing history item part to Content::parse', ['type' => gettype($partText), 'value' => $partText, 'role' => $role->value]);
                return Content::parse(part: $partText, role: $role);
            }, $data['history']);

            $promptText = (string) ($data['prompt'] ?? '');
            // Explicitly ensure it's a string, even if redundant, for defensive programming
            if (!is_string($promptText)) {
                Log::warning('Gemini: User prompt is not a string after cast, forcing conversion.', ['original_type' => gettype($promptText), 'original_value' => $promptText]);
                $promptText = (string) $promptText;
            }
            Log::debug('Gemini: Passing user prompt to Content::parse (with history)', ['type' => gettype($promptText), 'value' => $promptText, 'role' => Role::USER->value]);
            return [
                ...$history,
                Content::parse(part: $promptText, role: Role::USER),
            ];
        }

        $promptText = (string) ($data['prompt'] ?? '');
        Log::debug('Gemini: User prompt part type and value (no history)', ['type' => gettype($promptText), 'value' => $promptText]);

        $key = 'ai.prompts.' . $promptText;
        $promptTemplate = config($key);

        if ($promptTemplate) {
            return str_replace(':input', $data['input'] ?? '', $promptTemplate);
        }

        // Fallback to the main prompt if no specific type is matched
        return $promptText;
    }
}
