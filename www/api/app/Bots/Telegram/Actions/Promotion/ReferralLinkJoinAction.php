<?php

namespace App\Bots\Telegram\Actions\Promotion;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Models\TgReferralLink;
use App\Models\TgReferralLinkJoin;
use SergiX44\Nutgram\Telegram\Attributes\ChatMemberStatus;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class ReferralLinkJoinAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    public function __invoke(): void
    {
        $newStatus = TelegramWebhook::getPureData()['chat_member']['new_chat_member']['status'] ?? null;

        if (in_array($newStatus, [ChatMemberStatus::LEFT, ChatMemberStatus::KICKED])) {
            TgReferralLinkJoin::query()
                ->where('user_id', TelegramWebhook::getUser()->id)
                ->delete();
        }

        if (!TelegramWebhook::getData()->chat_member->invite_link) {
            return;
        }

        /** @var null|TgReferralLink $tgReferralLink */
        $referralLink = TgReferralLink::query()
            ->where('link', TelegramWebhook::getData()->chat_member->invite_link->invite_link)
            ->first();

        TgReferralLinkJoin::create([
            'link_id' => $referralLink->id ?? null,
            'link' => TelegramWebhook::getData()->chat_member->invite_link->invite_link,
            'user_id' => TelegramWebhook::getUser()->id,
        ]);
    }

    public static function getPaths(): array
    {
        return ['/^$/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::CHAT_MEMBER];
    }
}
