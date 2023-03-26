<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Actions\ActionContract;
use App\Bots\Telegram\Actions\Promotion\ReferralLinkAction;
use App\Bots\Telegram\Actions\Promotion\ReferralLinkJoinAction;
use App\Bots\Telegram\Actions\StartAction;
use App\Bots\Telegram\Dto\ActionRouteInfo;
use Illuminate\Support\Collection;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Common\Update;

class TelegramActionRouter
{
    /** @var Collection<int, ActionRouteInfo> */
    protected readonly Collection $routes;

    public function __construct()
    {
        $this->routes = collect([
            StartAction::getActionRouteInfo(),
            ReferralLinkAction::getActionRouteInfo(),
            ReferralLinkJoinAction::getActionRouteInfo(),
        ]);
    }

    /**
     * @param  Update  $webhookData
     * @return Collection<int, ActionContract>
     */
    public function getAction(Update $webhookData): Collection
    {
        $filteredRoutes = $this->routes->filter(function (ActionRouteInfo $actionRouteInfo) use ($webhookData) {
            return in_array($webhookData->getType(), $actionRouteInfo->availableWebhookTypes);
        });

        return (match ($webhookData->getType()) {
            UpdateTypes::MESSAGE => $filteredRoutes->filter(
                function (ActionRouteInfo $actionRouteInfo) use ($webhookData) {
                    $pregTest = false;
                    foreach ($actionRouteInfo->paths as $path) {
                        $pregTest = preg_match($path, $webhookData->getMessage()->text) === 1;
                        if ($pregTest) {
                            break;
                        }
                    }

                    return $pregTest;
                }
            ),
            UpdateTypes::CHAT_MEMBER => $filteredRoutes,
            default => collect([]),
        })->values()->map(function (ActionRouteInfo $actionRouteInfo) {
            return $actionRouteInfo->action;
        });
    }
}
