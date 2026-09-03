<?php

require_once __DIR__ . '/../src/Config.php';
require_once __DIR__ . '/../src/TelegramBot.php';
require_once __DIR__ . '/../src/OpenRouter.php';
require_once __DIR__ . '/../src/AIChat.php';

$config = new Config();

$telegram = new TelegramBot(
    $config->get('MAIN_BOT_TOKEN')
);

$openRouter = new OpenRouter(
    $config->get('OPENROUTER_API_KEY')
);

$aiChat = new AIChat($openRouter);

$update = file_get_contents('php://input');

if (!$update) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Telegram AI Chat Bot is running.'
    ]);
    exit;
}

$data = json_decode($update, true);

if (!is_array($data)) {
    http_response_code(400);
    exit('Invalid update');
}

try {
    $aiChat->handle($telegram, $data);
} catch (Throwable $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo 'Internal Server Error';
}
