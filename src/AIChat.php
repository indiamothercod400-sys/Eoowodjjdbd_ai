<?php

class AIChat
{
    private OpenRouter $openRouter;

    public function __construct(OpenRouter $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function handle(
        TelegramBot $telegram,
        array $update
    ): void {
        if (!isset($update['message'])) {
            return;
        }

        $message = $update['message'];

        $chat = $message['chat'] ?? [];
        $chatId = $chat['id'] ?? null;

        if ($chatId === null) {
            return;
        }

        $text = trim($message['text'] ?? '');

        if ($text === '') {
            return;
        }

        $messageId = $message['message_id'] ?? null;

        /*
         * /start
         */
        if (preg_match('/^\/start(?:@\w+)?$/i', $text)) {
            $welcome =
                "👋 Hello!\n\n" .
                "আমি একটি AI Chat Bot।\n" .
                "আপনি আমাকে যেকোনো প্রশ্ন করতে পারেন।\n\n" .
                "💬 শুধু আপনার message পাঠান, আমি AI-এর মাধ্যমে reply দেব।";

            $telegram->sendMessage(
                $chatId,
                $welcome,
                $messageId
            );

            return;
        }

        /*
         * /help
         */
        if (preg_match('/^\/help(?:@\w+)?$/i', $text)) {
            $help =
                "🤖 AI Chat Bot Help\n\n" .
                "শুধু আপনার প্রশ্ন বা message লিখে পাঠান।\n" .
                "আমি AI-এর মাধ্যমে উত্তর দেওয়ার চেষ্টা করব।";

            $telegram->sendMessage(
                $chatId,
                $help,
                $messageId
            );

            return;
        }

        /*
         * AI request
         */
        $messages = [
            [
                'role' => 'system',
                'content' =>
                    'You are a helpful, friendly AI assistant. ' .
                    'Answer the user clearly and accurately. ' .
                    'If the user writes in Bengali, answer in Bengali. ' .
                    'If the user writes in English, answer in English.'
            ],
            [
                'role' => 'user',
                'content' => $text
            ]
        ];

        try {
            $answer = $this->openRouter->chat($messages);

            $telegram->sendMessage(
                $chatId,
                $answer,
                $messageId
            );
        } catch (Throwable $e) {
            error_log(
                'AI Error: ' . $e->getMessage()
            );

            $telegram->sendMessage(
                $chatId,
                "⚠️ দুঃখিত, এই মুহূর্তে AI-এর কাছ থেকে উত্তর পাওয়া যাচ্ছে না। একটু পরে আবার চেষ্টা করুন।",
                $messageId
            );
        }
    }
}
