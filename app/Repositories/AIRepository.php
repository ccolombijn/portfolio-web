<?php

declare(strict_types=1);

namespace App\Repositories;

use Anthropic\Client as AnthropicClient;
use App\Contracts\AIRepositoryInterface;
use Gemini\Client as GeminiClient;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use OpenAI\Client as OpenAIClient;
use Spatie\PdfToText\Exceptions\BinaryNotFoundException;
use Spatie\PdfToText\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class AIRepository implements AIRepositoryInterface
{
    public function __construct(
        private readonly OpenAIClient $openai,
        private readonly GeminiClient $gemini,
        private readonly AnthropicClient $anthropic
    ) {}

    /**
     * Get available AI profile names.
     */
    public function getAvailableProfiles(): array
    {
        $storage = Storage::disk('public');
        $path = 'json/profiles';

        if (! $storage->exists($path)) {
            Log::warning("AI profiles directory not found: {$path}");

            return [];
        }

        $files = $storage->files($path);

        return collect($files)
            ->filter(fn ($file) => pathinfo($file, PATHINFO_EXTENSION) === 'json')
            ->map(fn ($file) => pathinfo($file, PATHINFO_FILENAME))
            ->values()
            ->all();
    }
    
    /**
     * Generate AI response based on the specified provider.
     */
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
    /**
     * Build OpenAI messages with given data.
     */
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
    /**
     * Handles streaming generation using the OpenAI client.
     */
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
     * Handles generation using the Gemini client without streaming.
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
    /**
     * Handles streaming generation using the Gemini client.
     */
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
     * Handles generation using the Anthropic client.
     */
    private function generateWithAnthropic(array $data): JsonResponse|StreamedResponse
    {
        try {
            $model = $data['model'] ?? config('ai.models.anthropic', 'claude-3-haiku-20240307');
            $messages = $this->buildAnthropicMessages($data);
            $systemPrompt = $this->buildBasePromptParts($data)['system_prompt'];

            if (! empty($data['stream'])) {
                return $this->streamAnthropicResponse($model, $messages, $systemPrompt);
            }

            $result = $this->anthropic->messages()->create([
                'model' => $model,
                'system' => $systemPrompt,
                'messages' => $messages,
                'max_tokens' => 4096, // Anthropic requires max_tokens
            ]);

            return response()->json(['response' => $result->content[0]->text]);
        } catch (Throwable $e) {
            Log::error('Failed to get a response from Anthropic.', ['exception' => $e]);
            return response()->json(['error' => 'Failed to get a response from Anthropic.'], 500);
        }
    }

    /**
     * Handles streaming generation using the Anthropic client.
     */
    private function streamAnthropicResponse(string $model, array $messages, string $systemPrompt): StreamedResponse
    {
        $stream = $this->anthropic->messages()->createStreamed([
            'model' => $model,
            'system' => $systemPrompt,
            'messages' => $messages,
            'max_tokens' => 4096,
        ]);

        return new StreamedResponse(function () use ($stream) {
            try {
                foreach ($stream as $result) {
                    if ($result->type === 'content_block_delta') {
                        $content = $result->delta->text;
                        if (null !== $content) {
                            echo $content;
                            if (ob_get_level() > 0) {
                                ob_flush();
                            }
                            flush();
                        }
                    }
                }
            } catch (Throwable $e) {
                Log::error('An unexpected error occurred during the Anthropic stream.', ['exception' => $e]);
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

    private function buildAnthropicMessages(array $data): array
    {
        $allMessages = [];

        if (! empty($data['history'])) {
            foreach ($data['history'] as $item) {
                $role = $item['role'] ?? 'user';
                if (in_array($role, ['user', 'assistant'])) {
                    $allMessages[] = ['role' => $role, 'content' => (string) ($item['text'] ?? '')];
                }
            }
        }

        $baseParts = $this->buildBasePromptParts($data);
        $prompt = trim(implode("\n\n", array_filter([$baseParts['file_context'], $baseParts['prompt']])));

        $allMessages[] = ['role' => 'user', 'content' => $prompt];

        if (empty($allMessages)) {
            return [];
        }

        // Merge consecutive messages from the same role to comply with Anthropic's format.
        $messages = [];
        $lastMessage = array_shift($allMessages);

        foreach ($allMessages as $currentMessage) {
            if ($currentMessage['role'] === $lastMessage['role']) {
                $lastMessage['content'] .= "\n\n" . $currentMessage['content'];
            } else {
                $messages[] = $lastMessage;
                $lastMessage = $currentMessage;
            }
        }
        $messages[] = $lastMessage;

        return $messages;
    }

    /**
     * Build the base prompt including file contexts.
     */
    private function buildBasePrompt(array $data): string
    {
        $parts = $this->buildBasePromptParts($data);

        return trim(implode("\n\n", array_filter($parts)));
    }
    /**
     * Build prompt with given data
     * @return array{system_prompt: string, file_context: string, prompt: string}
     */
    private function buildBasePromptParts(array $data): array
    {   
        $profileName = $data['profile'] ?? config('ai.default_profile');
        $profileData = null;
        if ($profileName) {
            $profileData = $this->loadProfile($profileName);
        }

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
        if (! $isPredefinedPrompt) {
            $defaultFiles = $profileData['files'] ?? config('ai.default_files', []);
            $requestFiles = $data['file_paths'] ?? [];
            $allFiles = array_unique(array_merge($defaultFiles, $requestFiles));

            foreach ($allFiles as $filePath) {
                $fileContent = $this->getFileContentForPrompt($filePath);
                if (null !== $fileContent) {
                    $fileName = basename($filePath);
                    $fileContext .= "File: `{$fileName}`\n\n```\n{$fileContent}\n```\n\n";
                }
            }
        }

        return [
            'system_prompt' => $data['system_prompt'] ?? $profileData['system_prompt'] ?? config('ai.system_prompt', ''),
            'file_context' => $fileContext,
            'prompt' => $prompt,
        ];
    }
    /**
     * Load AI profile from JSON file.
     * @param string $profileName
     */
    private function loadProfile(string $profileName): ?array
    {
        // Sanitize profile name to prevent directory traversal
        $profileName = basename($profileName);
        $path = "json/profiles/{$profileName}.json";
        $storage = Storage::disk('public');

        if (! $storage->exists($path)) {
            Log::warning("AI profile not found: {$path}");
            return null;
        }

        try {
            $content = $storage->get($path);
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::error("Failed to parse AI profile: {$path}", ['exception' => $e]);
            return null;
        }
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
            } catch (BinaryNotFoundException $e) {
                Log::critical('pdftotext binary not found. Please install poppler-utils on your system.', ['exception' => $e]);
                return null;
            } catch (Throwable $e) {
                Log::error("Failed to extract text from PDF: {$fullPath}", ['exception' => $e]);
                return "Error: Could not extract text from PDF file '{$filePath}'.";
            }
        }

        return $storage->get($filePath);
    }
}
