<?php

class TelegramBot
{
    private string $token;
    private string $apiUrl;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->apiUrl = "https://api.telegram.org/bot{$token}/";
    }

    public function request(string $method, array $params = []): array
    {
        $url = $this->apiUrl . $method;

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new RuntimeException(
                'Telegram API error: ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                'Invalid Telegram API response.'
            );
        }

        if ($httpCode >= 400 || !($data['ok'] ?? false)) {
            throw new RuntimeException(
                'Telegram API request failed: ' . $response
            );
        }

        return $data;
    }

    public function sendMessage(
        int|string $chatId,
        string $text,
        ?int $replyTo = null
    ): array {
        $params = [
            'chat_id' => $chatId,
            'text' => $text
        ];

        if ($replyTo !== null) {
            $params['reply_parameters'] = json_encode([
                'message_id' => $replyTo
            ]);
        }

        return $this->request('sendMessage', $params);
    }

    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url
        ]);
    }

    public function deleteWebhook(): array
    {
        return $this->request('deleteWebhook');
    }

    public function getMe(): array
    {
        return $this->request('getMe');
    }
}
