<?php

namespace Database\Seeders;

use Database\Seeders\Traits\EnumUpdate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OptionGroupSlugSeeder extends Seeder
{
    use EnumUpdate;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableTypes = Arr::pluck(\App\Enums\OptionGroupSlug::cases(), 'value');

        $this->updateEnum('list_options', 'group_slug', $availableTypes);
    }
}
