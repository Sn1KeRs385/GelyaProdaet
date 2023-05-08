<?php

namespace App\Utils;


class ColorGenerator
{
    protected array $colors = [
        '1E90FF',
        '32CD32',
        'A0522D',
        'D2691E',
        'DAA520',
        'F4A460',
        '4B0082',
        '8A2BE2',
        'EE82EE',
        'CD5C5C',

        '000080',
        '0000FF',
        '008080',
        '00FFFF',
        '008000',
        '00FF00',
        '808000',
        'FFFF00',
        '800000',
        'FF0000',
        '800080',
        'FF00FF',
    ];

    protected function generateRandomColor(): string
    {
        return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function getNextColor(bool $withHashSign = false): string
    {
        if (count($this->colors) > 0) {
            $color = array_pop($this->colors);
        } else {
            $color = $this->generateRandomColor();
        }

        if ($withHashSign) {
            return "#{$color}";
        } else {
            return $color;
        }
    }
}
