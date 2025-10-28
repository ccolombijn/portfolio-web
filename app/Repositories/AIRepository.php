<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AIRepositoryInterface;
use Gemini\Client as GeminiClient;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use OpenAI\Client as OpenAIClient;
use Spatie\PdfToText\Pdf;
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
        //Log::debug('AI generation requested.', ['provider' => $provider, 'data' => $data]);
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
            $model = $data['model'] ?? config('ai.models.openai', 'gpt-5-nano');
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

        $prompt = $this->buildBasePrompt($data);

        $messages[] = ['role' => 'user', 'content' => $prompt];
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
        try {
            $model = $data['model'] ?? config('ai.models.gemini', 'gemini-2.5-flash');
            $prompt = $this->buildGeminiPrompt($data);

            if (! empty($data['stream'])) {
                return $this->streamGeminiResponse($model, $prompt);
            }
            // Non-streaming response
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
        $basePromptParts = $this->buildBasePromptParts($data);

        if (isset($data['history'])) {
            $history = [];

            // Add system prompt and file context as initial user messages if not already in history
            if (!empty($basePromptParts['system_prompt'])) {
                $history[] = Content::parse(part: $basePromptParts['system_prompt'], role: Role::USER);
                $history[] = Content::parse(part: 'Ok, begrepen.', role: Role::MODEL); // Acknowledge the system prompt
            }
            if (!empty($basePromptParts['file_context'])) {
                $history[] = Content::parse(part: $basePromptParts['file_context'], role: Role::USER);
                $history[] = Content::parse(part: 'Ok, ik heb de bestanden gelezen.', role: Role::MODEL); // Acknowledge the file context
            }

            foreach ($data['history'] as $item) {
                $role = ($item['role'] ?? 'user') === 'model' ? Role::MODEL : Role::USER;
                $partText = (string) ($item['text'] ?? '');
                $history[] = Content::parse(part: $partText, role: $role);
            }

            return [...$history, Content::parse(part: $basePromptParts['prompt'], role: Role::USER)];
        }

        return trim(implode("\n\n", array_filter($basePromptParts)));
    }

    /**
     * Build the base prompt including file contexts.
     */
    private function buildBasePrompt(array $data): string
    {
        $parts = $this->buildBasePromptParts($data);

        return trim(implode("\n\n", array_filter($parts)));
    }

    private function buildBasePromptParts(array $data): array
    {
        $prompt = (string) ($data['prompt'] ?? '');
        $isPredefinedPrompt = array_key_exists($prompt, config('ai.prompts', []));

        // If the prompt is a key for a predefined prompt, get the full text.
        if ($isPredefinedPrompt) {
            $promptTemplate = config('ai.prompts.' . $prompt);
            $prompt = str_replace(':input', $data['input'] ?? '', $promptTemplate);
        }

        $fileContext = '';
        // Only add file context if it's not a predefined prompt like 'explanation' or 'summarize'
        // We check the original prompt key here, not the full text.
        if (!$isPredefinedPrompt) {
            $defaultFiles = config('ai.default_files', []);
            $requestFiles = $data['file_paths'] ?? [];
            $allFiles = array_unique(array_merge($defaultFiles, $requestFiles));

            foreach ($allFiles as $filePath) {
                $fileContent = $this->getFileContentForPrompt($filePath);
                if ($fileContent !== null) {
                    $fileName = basename($filePath);
                    $fileContext .= "File: `{$fileName}`\n\n```\n{$fileContent}\n```\n\n";
                }
            }
        }

        return [
            'system_prompt' => config('ai.system_prompt', ''),
            'file_context' => $fileContext,
            'prompt' => $prompt,
        ];
    }

    // Sanitize and restrict path to storage/app/public
    private function getFileContentForPrompt(string $filePath): ?string
    {
        // Sanitize and restrict path to storage/app/public
        $filePath = str_replace('..', '', $filePath);

        $storage = Storage::disk('public');

        if (! $storage->exists($filePath)) {
            Log::warning('File path could not be found in storage/app/public.', ['path' => $filePath]);
            return null;
        }

        $fullPath = $storage->path($filePath);
        $realPath = realpath($fullPath);

        if (! $realPath || ! str_starts_with($realPath, realpath($storage->path('')))) {
            Log::warning('File path access attempt outside of storage/app/public.', ['path' => $filePath]);
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            try {
                return Pdf::getText($fullPath);
            } catch (Throwable $e) {
                Log::error("Failed to extract text from PDF: {$fullPath}", ['exception' => $e]);
                return "Error: Could not extract text from PDF file '{$filePath}'.";
            }
        }

        return $storage->get($filePath);
    }
}
