<?php

namespace Database\Seeders;

use App\Enums\OptionGroupSlug;
use App\Models\ListOption;
use Illuminate\Database\Seeder;

class ListOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ListOption::query()->count() > 0) {
            return;
        }

        $data = [
            [
                'group_slug' => OptionGroupSlug::BRAND,
                'title' => 'H&M',
            ],
            [
                'group_slug' => OptionGroupSlug::BRAND,
                'title' => 'Zara',
            ],

            [
                'group_slug' => OptionGroupSlug::COUNTRY,
                'title' => 'Турция',
                'weight' => 500,
            ],
            [
                'group_slug' => OptionGroupSlug::COUNTRY,
                'title' => 'Германия',
                'weight' => 400,
            ],
            [
                'group_slug' => OptionGroupSlug::COUNTRY,
                'title' => 'Россия',
            ],
            [
                'group_slug' => OptionGroupSlug::COUNTRY,
                'title' => 'Китай',
            ],

            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Костюм',
            ],
            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Футболка',
            ],
            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Штаны',
            ],
            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Куртка',
            ],
            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Платье',
            ],
            [
                'group_slug' => OptionGroupSlug::PRODUCT_TYPE,
                'title' => 'Юбка',
            ],

            [
                'group_slug' => OptionGroupSlug::COLOR,
                'title' => 'Белый',
            ],
            [
                'group_slug' => OptionGroupSlug::COLOR,
                'title' => 'Черный',
            ],
            [
                'group_slug' => OptionGroupSlug::COLOR,
                'title' => 'Синий',
            ],
            [
                'group_slug' => OptionGroupSlug::COLOR,
                'title' => 'Красный',
            ],
            [
                'group_slug' => OptionGroupSlug::COLOR,
                'title' => 'Серый',
            ],

            [
                'group_slug' => OptionGroupSlug::GENDER,
                'title' => 'На мальчика',
            ],
            [
                'group_slug' => OptionGroupSlug::GENDER,
                'title' => 'На девочку',
            ],
            [
                'group_slug' => OptionGroupSlug::GENDER,
                'title' => 'На мальчика и девочку',
            ],
        ];

        foreach ($data as $item) {
            ListOption::create($item);
        }

        for ($i = 50; $i <= 152; $i += 6) {
            ListOption::create([
                'group_slug' => OptionGroupSlug::SIZE,
                'title' => $i,
                'weight' => 1000000 - ($i * 100),
            ]);
        }
        for ($i = 2; $i < 16; $i++) {
            ListOption::create([
                'group_slug' => OptionGroupSlug::SIZE,
                'title' => $i . '-' . ($i + 1),
                'weight' => 800000 - ($i * 100),
            ]);
        }
    }
}
