<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CodeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableTypes = Arr::pluck(\App\Enums\CodeType::cases(), 'value');

        if (config('database.default') === 'mysql') {
            $availableTypes = array_map(fn($item) => "'$item'", $availableTypes);
            $availableTypes = implode(', ', $availableTypes);

            DB::statement(
                "ALTER TABLE `user_codes` CHANGE `type` `type` ENUM($availableTypes) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
            );
        } elseif (config('database.default') === 'pgsql') {
            DB::transaction(function () use ($availableTypes) {
                DB::statement("ALTER TABLE user_codes DROP CONSTRAINT user_codes_type_check");

                $availableTypes = join(
                    ', ',
                    array_map(function ($value) {
                        return sprintf("'%s'::character varying", $value);
                    }, $availableTypes)
                );

                DB::statement(
                    "ALTER TABLE user_codes ADD CONSTRAINT user_codes_type_check CHECK (type::text = ANY (ARRAY[$availableTypes]::text[]))"
                );
            });
        }
    }
}
