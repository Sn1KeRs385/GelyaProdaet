<?php

namespace App\Bots\Telegram;

use App\Bots\Telegram\Actions\ActionContract;
use App\Bots\Telegram\Actions\Filters\FilterBrandAction;
use App\Bots\Telegram\Actions\Filters\FilterCountryAction;
use App\Bots\Telegram\Actions\Filters\FilterGenderAction;
use App\Bots\Telegram\Actions\Filters\FilterSetAction;
use App\Bots\Telegram\Actions\Filters\FilterSizeAction;
use App\Bots\Telegram\Actions\Product\ProductIndexAction;
use App\Bots\Telegram\Actions\Product\ProductRequestAction;
use App\Bots\Telegram\Actions\Product\ProductRequestNextAction;
use App\Bots\Telegram\Actions\Promotion\ReferralLinkAction;
use App\Bots\Telegram\Actions\Promotion\ReferralLinkJoinAction;
use App\Bots\Telegram\Actions\StartAction;
use App\Bots\Telegram\Actions\System\CheckDeleteAction;
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
            ProductIndexAction::getActionRouteInfo(),
            ProductRequestAction::getActionRouteInfo(),
            ProductRequestNextAction::getActionRouteInfo(),
            FilterBrandAction::getActionRouteInfo(),
            FilterCountryAction::getActionRouteInfo(),
            FilterGenderAction::getActionRouteInfo(),
            FilterSizeAction::getActionRouteInfo(),
            FilterSetAction::getActionRouteInfo(),
            CheckDeleteAction::getActionRouteInfo(),
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
            UpdateTypes::MESSAGE => $this->getRoutesListFromText($filteredRoutes, $webhookData->getMessage()->text),
            UpdateTypes::CALLBACK_QUERY => $this->getRoutesListFromText(
                $filteredRoutes,
                $webhookData->callback_query->data
            ),
            UpdateTypes::CHAT_MEMBER => $filteredRoutes,
            default => collect([]),
        })
            ->values()
            ->map(function (ActionRouteInfo $actionRouteInfo) {
                return $actionRouteInfo->action;
            });
    }

    public function getActionByText(string $text): Collection
    {
        return $this->getRoutesListFromText($this->routes, $text)
            ->values()
            ->map(function (ActionRouteInfo $actionRouteInfo) {
                return $actionRouteInfo->action;
            });
    }

    /**
     * @param  Collection<int, ActionRouteInfo>  $routes
     * @param  string|null  $text
     * @return Collection<int, ActionContract>
     */
    protected function getRoutesListFromText(Collection $routes, ?string $text): Collection
    {
        if(!$text) {
            return collect([]);
        }
        
        return $routes->filter(function (ActionRouteInfo $actionRouteInfo) use ($text) {
            $pregTest = false;
            foreach ($actionRouteInfo->paths as $path) {
                $pregTest = preg_match($path, $text) === 1;
                if ($pregTest) {
                    break;
                }
            }

            return $pregTest;
        });
    }
}
