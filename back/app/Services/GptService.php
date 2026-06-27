<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Central place for talking to the KIConnect (OpenAI-compatible) endpoint.
 *
 * Inject it anywhere:
 *
 *   public function __construct(private GptService $gpt) {}
 *
 *   $text  = $this->gpt->ask('Explain recursion in one sentence.');
 *   $json  = $this->gpt->askJson($prompt, $systemPrompt);
 *   $resp  = $this->gpt->chat($messages, ['temperature' => 0.2]);
 */
class GptService
{
    private string $defaultModel = 'openai-gpt-oss-120b_stud';

    /**
     * Low-level call. Returns the raw HTTP response so callers can inspect
     * status/headers themselves if they need to.
     *
     * @param  array  $messages  OpenAI-style [['role'=>..,'content'=>..], ...]
     * @param  array  $options    model|temperature|max_tokens|json|timeout
     */
    public function chat(array $messages, array $options = []): Response
    {
        $payload = array_filter([
            'model'           => $options['model'] ?? $this->defaultModel,
            'messages'        => $messages,
            'temperature'     => $options['temperature'] ?? null,
            'max_tokens'      => $options['max_tokens'] ?? null,
            // Hint for servers that support it; harmless if ignored.
            'response_format' => !empty($options['json']) ? ['type' => 'json_object'] : null,
        ], fn ($v) => $v !== null);

        return Http::withToken(config('services.kiconnect.api_key'))
            ->timeout($options['timeout'] ?? 120)
            ->post(config('services.kiconnect.url'), $payload);
    }

    /**
     * Send a single prompt (with optional system message) and get the text back.
     *
     * @throws RuntimeException on a failed request.
     */
    public function ask(string $prompt, ?string $system = null, array $options = []): string
    {
        $response = $this->chat($this->buildMessages($prompt, $system), $options);

        if ($response->failed()) {
            throw new RuntimeException(
                'KIConnect request failed (HTTP ' . $response->status() . ')'
            );
        }

        return (string) data_get($response->json(), 'choices.0.message.content', '');
    }

    /**
     * Like ask(), but expects the model to return JSON and parses it.
     * Sets the json hint automatically.
     *
     * @throws RuntimeException if the request fails or no JSON can be parsed.
     */
    public function askJson(string $prompt, ?string $system = null, array $options = []): array
    {
        $content = $this->ask($prompt, $system, ['json' => true] + $options);
        $parsed  = $this->extractJson($content);

        if ($parsed === null) {
            throw new RuntimeException('Model did not return valid JSON: ' . $content);
        }

        return $parsed;
    }

    /**
     * Build a messages array from a prompt and optional system message.
     */
    public function buildMessages(string $prompt, ?string $system = null): array
    {
        $messages = [];
        if ($system !== null && $system !== '') {
            $messages[] = ['role' => 'system', 'content' => $system];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        return $messages;
    }

    /**
     * Pull a JSON object out of a model reply.
     * gpt-oss models sometimes wrap it in ```fences``` or prepend reasoning,
     * so strip fences first, then fall back to the outermost {...} block.
     */
    public function extractJson(string $content): ?array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($content, '{');
        $end   = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($content, $start, $end - $start + 1), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}