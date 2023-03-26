<?php

namespace App\Http\Middleware\Telegram;

use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Enums\IdentifierType;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserDetect
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->userRepository->findOrCreateByIdentifier(
            IdentifierType::TG_USER_ID,
            TelegramWebhook::getData()->getUser()->id
        );
        TelegramWebhook::setUser($user);
        return $next($request);
    }
}
