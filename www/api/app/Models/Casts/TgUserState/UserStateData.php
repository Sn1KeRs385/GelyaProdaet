<?php

namespace App\Models\Casts\TgUserState;

use App\Bots\Telegram\Actions\ActionContract;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class UserStateData extends Data implements Castable
{
    public Filters $filters;

    /** @var ActionContract[] */
    protected ?array $actionsQueue = [];

    public ?int $productRequestStep;
    /** @var ProductMessageToSend[] */
    public DataCollection $productMessagesToSend;

    public static function from(...$payloads): static
    {
        $dto = parent::from(...$payloads);
        if (!isset($dto->filters)) {
            $dto->filters = new Filters();
        }
        if (!isset($dto->productMessagesToSend)) {
            $dto->productMessagesToSend = new DataCollection(ProductMessageToSend::class, []);
        }

        return $dto;
    }

    public static function castUsing(array $arguments): string
    {
        return UserStateDataCaster::class;
    }

    public function clearForSave(): self
    {
        $this->actionsQueue = null;
        return $this;
    }

    public function getNextAction(): ?ActionContract
    {
        return array_shift($this->actionsQueue);
    }

    public function addActionsToQueue(ActionContract ...$actions): self
    {
        foreach ($actions as $action) {
            $this->addActionToQueue($action);
        }
        return $this;
    }

    public function addActionToQueue(ActionContract $action): self
    {
        if (!$this->actionsQueue) {
            $this->actionsQueue = [$action];
        } else {
            $this->actionsQueue[] = $action;
        }
        return $this;
    }
}
