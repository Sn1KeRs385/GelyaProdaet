<?php

namespace Database\Seeders;

use Database\Seeders\Traits\EnumUpdate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CompilationTypeSeeder extends Seeder
{
    use EnumUpdate;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableTypes = Arr::pluck(\App\Enums\CompilationType::cases(), 'value');

        $this->updateEnum('compilations', 'type', $availableTypes);
    }
}
