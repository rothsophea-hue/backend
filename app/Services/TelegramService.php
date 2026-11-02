<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramService
{
    public function send(string $message): void
    {
        $token = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            throw new RuntimeException('Telegram is not configured.');
        }

        $response = Http::timeout(10)
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

        $response->throw();
        if ($response->json('ok') !== true) {
            throw new RuntimeException('Telegram rejected the message.');
        }
    }
}

