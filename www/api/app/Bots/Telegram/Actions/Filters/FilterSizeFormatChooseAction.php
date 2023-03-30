<?php

namespace App\Bots\Telegram\Actions\Filters;

use App\Enums\OptionGroupSlug;

class FilterSizeFormatChooseAction extends AbstractFilterFormatChooseAction
{
    protected function getAllText(): string
    {
        return 'Любой';
    }

    protected function getLabelText(): string
    {
        return "Выберите один или несколько размеров\nВы выбрали: ";
    }

    protected function getOptionGroupSlug(): OptionGroupSlug
    {
        return OptionGroupSlug::SIZE;
    }
}
