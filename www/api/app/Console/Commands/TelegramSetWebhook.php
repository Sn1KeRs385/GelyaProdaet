<?php

namespace App\Console\Commands;

use App\Bots\Telegram\TelegramBot;
use Illuminate\Console\Command;

class TelegramSetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-web-hook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set webhook for new messages';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $bot = new TelegramBot(config('telegram.bot_api_key'));
        $bot->setWebhook(
            route('api.telegram.webhook'),
//            'https://webhook.site/f54b8e70-aed4-4e37-bae0-20db650b814d',
            [
                'secret_token' => config('telegram.webhook_token'),
                'allowed_updates' => [
                    'message',
                    'callback_query',
                    'chat_join_request',
                    'chat_member',
                ],
            ]
        );
    }
}
