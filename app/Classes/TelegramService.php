<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = '7965970557:AAHV2-bdkEuXB6tV9qVni7wTKFsRtI3f7Mc';
        $this->chatId = '-1002470649272';
    }

    public function sendMessage($message)
    {

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        return Http::withOptions(['proxy'=>'https://t.me/proxy?server=193.68.89.7&port=443&secret=15115115115115115115115115115115'])->post($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }
}
