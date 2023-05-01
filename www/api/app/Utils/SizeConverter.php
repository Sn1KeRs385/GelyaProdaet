<?php

namespace App\Utils;


class SizeConverter
{
    public const SIZE_TO_YEAR_LIST = [
        92 => [1, 2],
        98 => [2, 3],
        104 => [3, 4],
        110 => [4, 5],
        116 => [5, 6],
        122 => [6, 7],
        128 => [7, 8],
        134 => [8, 9],
        140 => [9, 10],
        146 => [10, 11],
        152 => [12, 13],
        158 => [14, 15],
        164 => [16, 17],
    ];

    public function getYearFromSize(string $sizes): array
    {
        $years = [];

        $sizes = explode('-', $sizes);
        foreach ($sizes as $size) {
            if (isset(self::SIZE_TO_YEAR_LIST[$size])) {
                $years = array_merge($years, self::SIZE_TO_YEAR_LIST[$size]);
            } else {
                $prevSize = 86;
                foreach (self::SIZE_TO_YEAR_LIST as $sizeFromTable => $yearsFromTable) {
                    if ($size < $sizeFromTable && $size > $prevSize) {
                        $sizesOfPrev = self::SIZE_TO_YEAR_LIST[$prevSize] ?? [];
                        if(count($sizesOfPrev) > 0) {
                            $sizesOfPrev = [end($sizesOfPrev)];
                        }
                        $years = array_merge(
                            $years,
                            [$yearsFromTable[0]],
                            $sizesOfPrev
                        );
                    }

                    $prevSize = $sizeFromTable;
                }
            }
        }

        return $years;
    }

    public function getSizeFromYear(string $years): array
    {
        $sizes = [];

        $years = explode('-', $years);
        foreach ($years as $year) {
            foreach (self::SIZE_TO_YEAR_LIST as $sizeFromTable => $yearsFromTable) {
                if (in_array($year, $yearsFromTable)) {
                    $sizes[] = $sizeFromTable;
                }
            }
        }

        return $sizes;
    }
}
