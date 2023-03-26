<?php

namespace App\Bots\Telegram\Actions\Promotion;

use App\Bots\Telegram\Actions\AbstractAction;
use App\Bots\Telegram\Actions\Traits\ActionRouteInfoMapper;
use App\Bots\Telegram\Facades\TelegramWebhook;
use App\Bots\Telegram\TelegramBot;
use App\Models\TgReferralLink;
use App\Models\TgReferralLinkJoin;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class ReferralLinkAction extends AbstractAction
{
    use ActionRouteInfoMapper;

    public function __invoke(): void
    {
        /** @var null|TgReferralLink $referralLink */
        $referralLink = TelegramWebhook::getUser()->tgReferralLinks()->first();
        if (!$referralLink) {
            $referralLink = new TgReferralLink();
            $referralLink->user_id = TelegramWebhook::getUser()->id;
            $referralLink->name = 'PIL-'
                . TelegramWebhook::getData()->getUser()->id
                . '-'
                . TelegramWebhook::getUser()->id;

            $link = TelegramWebhook::getBot()->createChatInviteLink(
                TelegramBot::getPublicId(),
                ['name' => $referralLink->name]
            );
            $referralLink->link = $link->invite_link;
            $referralLink->save();
        }

        if ($referralLink->wasRecentlyCreated) {
            $text = "Приветствуем вас в программе \"Пригласи друга\"! Рады видеть вас здесь и благодарим за участие.";
        } else {
            $inviteCount = TgReferralLinkJoin::query()
                ->select('link_id')
                ->selectRaw('count(distinct(user_id)) as user_count')
                ->where('link_id', $referralLink->id)
                ->groupBy('link_id')
                ->first()
                ->user_count ?? 0;

            $text = "Вы уже принимаете участие в программе \"Пригласи друга\"";
            $text .= "\nКоличество новый участников по вашей ссылке: {$inviteCount}";
        }
        $text .= "\n\nВаша ссылка для приглашений: $referralLink->link";
        if ($referralLink->wasRecentlyCreated) {
            $text .= "\nЧтобы узнать количество приглашенных вами участников, отправьте сообщение с текстом \"Пригласи друга\" в этот чат повторно";
        }
        $text .= "\n\nОбращаем ваше внимание, что мы учитываем только тех друзей, которые присоединились к нам именно по вашей ссылке и не вышли из группы.";

        TelegramWebhook::getBot()->sendMessage($text, [
            'chat_id' => TelegramWebhook::getData()->getChat()->id
        ]);
    }

    public static function getPaths(): array
    {
        return ['/^пригла\S*\sдру(г|з)\S*$/ui', '/^\/promotion_invite_friend/ui'];
    }

    public static function getAvailableWebhookTypes(): array
    {
        return [UpdateTypes::MESSAGE];
    }
}
