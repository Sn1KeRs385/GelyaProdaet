<?php

namespace App\Console\Commands;

use App\Models\TgMessage;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class TelegramDeleteMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:delete-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete TgMessage model messages';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        TgMessage::onlyTrashed()
            ->where('is_messages_deleted', false)
            ->chunk(500, function (Collection $tgMessages) {
                /** @var Collection<int, TgMessage>|TgMessage[] $tgMessages */
                foreach ($tgMessages as $tgMessage) {
                    \App\Jobs\TelegramDeleteMessages::dispatch($tgMessage->id)
                        ->onQueue('tg_system_notification');
                }
            });
    }
}
