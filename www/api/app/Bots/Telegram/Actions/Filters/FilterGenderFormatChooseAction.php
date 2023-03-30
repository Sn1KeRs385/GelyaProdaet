<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Enums\OptionGroupSlug;

class FilterGenderFormatChooseAction extends AbstractFilterFormatChooseAction
{
    protected function getAllText(): string
    {
        return 'И на мальчика и на девочку';
    }

    protected function getLabelText(): string
    {
        return "На кого вы хотели бы посмотреть вещи?\nВы выбрали: ";
    }

    protected function getOptionGroupSlug(): OptionGroupSlug
    {
        return OptionGroupSlug::GENDER;
    }
}
