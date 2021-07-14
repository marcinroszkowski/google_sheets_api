<?php

namespace App\Traits;

/**
 * Trait HelperTrait
 * @package App\Traits
 */
trait HelperTrait
{
    /**
     * @param array $data
     * @return array
     */
    public function wrapArrayElementWithArray(array $data): array
    {
        return array_map(function ($row) {
            return [$row];
        }, $data);
    }

    /**
     * @param string $column
     * @param array $row
     * @return string
     */
    private function getRange(string $column, array $row): string
    {
        $rowElements = count($row);

        return 'congress!'.$column.self::SHEET_STARTING_ROW.':'.$column.$rowElements;
    }
}