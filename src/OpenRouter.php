<?php

class OpenRouter
{
    private string $apiKey;
    private string $endpoint =
        'https://openrouter.ai/api/v1/chat/completions';

    private string $model = 'openrouter/free';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function chat(array $messages): string
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages
        ];

        $ch = curl_init($this->endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'HTTP-Referer: https://your-app.onrender.com',
                'X-Title: Telegram AI Chat Bot'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CONNECTTIMEOUT => 15
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new RuntimeException(
                'OpenRouter connection error: ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                'Invalid OpenRouter response.'
            );
        }

        if ($httpCode >= 400) {
            $message = $data['error']['message']
                ?? 'Unknown OpenRouter error.';

            throw new RuntimeException(
                'OpenRouter error: ' . $message
            );
        }

        $content = $data['choices'][0]['message']['content'] ?? '';

        if ($content === '') {
            throw new RuntimeException(
                'AI returned an empty response.'
            );
        }

        return trim($content);
    }
}
