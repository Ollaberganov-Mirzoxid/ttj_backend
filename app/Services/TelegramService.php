<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    // Bot tokenini .env fayldan olish
    protected $telegramBotToken;
    protected $telegramUrl;

    public function __construct()
    {
        // Telegram bot tokenini olish
        $this->telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        // Telegram API endpoint
        $this->telegramUrl = "https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage";
    }

    // Xabar yuborish metodi
    public function sendMessage($chatId, $message)
    {
        try {
            // Telegram API orqali xabar yuborish
            Http::post($this->telegramUrl, [
                'chat_id' => $chatId,  // Chat ID
                'text' => $message      // Xabar matni
            ]);
        } catch (\Exception $e) {
            // Agar xatolik yuz bersa, log yozish
            \Log::error('Telegram message sending failed: ' . $e->getMessage());
        }
    }
}
