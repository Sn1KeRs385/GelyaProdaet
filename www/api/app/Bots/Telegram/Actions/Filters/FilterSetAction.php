<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Actions\Traits\BackParamHandler;
use App\Bots\Telegram\Actions\Traits\CallbackQueryMethods;
use App\Bots\Telegram\Actions\Traits\ParamsParse;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Models\ListOption;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class FilterSetAction extends AbstractAction
{
    use ActionRouteInfoMapper;
    use ParamsParse;
    use CallbackQueryMethods;
    use BackParamHandler;

    public function __invoke(): void
    {
        $params = $this->getParamsFromWebhookData(TelegramWebhook::getFacadeRoot());

        $state = &TelegramWebhook::getState()->data;

        if ($params['id'] === 'null' && count($state->filters->listOptionIds) !== 0) {
            $listOptionIds = ListOption::query()
                ->select('id', 'slug')
                ->where('group_slug', $params['slug'])
                ->whereIn('id', $state->filters->listOptionIds ?? [])
                ->pluck('id');

            $state->filters->listOptionIds = array_diff(
                $state->filters->listOptionIds,
                $listOptionIds
            );
        } else {
            $id = (int)$params['id'];
            if (!in_array($id, $state->filters->listOptionIds)) {
                $state->filters->listOptionIds[] = $id;
                if (filter_var($params['clear'] ?? false, FILTER_VALIDATE_BOOL) && isset($params['slug'])) {
                    $optionIds = ListOption::query()
                        ->select('id', 'group_slug')
                        ->where('group_slug', $params['slug'])
                        ->where('id', '<>', $id)
                        ->pluck('id');
                    $state->filters->listOptionIds = array_diff(
                        $state->filters->listOptionIds,
                        $optionIds
                    );
                }
            } else {
                $state->filters->listOptionIds = array_diff($state->filters->listOptionIds, [$id]);
            }
        }

        if (isset($params['back'])) {
            $this->handleBackParam($params['back'], TelegramWebhook::getFacadeRoot());
        }

        if (filter_var($params['del'] ?? false, FILTER_VALIDATE_BOOL)) {
            $this->deleteCallbackQueryMessage(TelegramWebhook::getFacadeRoot());
        }
    }

    public static function getPaths(): array
    {
        return ['/^\/filterSet/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE, UpdateTypes::CALLBACK_QUERY];
    }
}
