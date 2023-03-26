<?php

namespace App\Http\Middleware\Telegram;

use App\Bots\Telegram\TelegramBot;
use App\Bots\Telegram\TelegramWebhook;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use SergiX44\Nutgram\Hydrator\Hydrator;
use SergiX44\Nutgram\Telegram\Types\Common\Update;
use Symfony\Component\HttpFoundation\Response;

class WebhookDataExtract
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bot = new TelegramBot(config('telegram.bot_api_key'));

        $input = file_get_contents('php://input');
        /** @var Update $update */
        $update = $bot->getContainer()
            ->get(Hydrator::class)
            ->hydrate(json_decode($input, flags: JSON_THROW_ON_ERROR), Update::class);

        App::bind('TelegramWebhookData', function () use ($bot, $update, $request) {
            return new TelegramWebhook($bot, $update, $request->all());
        });

        return $next($request);
    }
}
