<?php

class Config
{
    private array $values;

    public function __construct()
    {
        $this->values = [
            'MAIN_BOT_TOKEN' => getenv('MAIN_BOT_TOKEN') ?: '',
            'OPENROUTER_API_KEY' => getenv('OPENROUTER_API_KEY') ?: ''
        ];

        if ($this->values['MAIN_BOT_TOKEN'] === '') {
            throw new RuntimeException(
                'MAIN_BOT_TOKEN is not configured.'
            );
        }

        if ($this->values['OPENROUTER_API_KEY'] === '') {
            throw new RuntimeException(
                'OPENROUTER_API_KEY is not configured.'
            );
        }
    }

    public function get(string $key): string
    {
        return $this->values[$key] ?? '';
    }
}
